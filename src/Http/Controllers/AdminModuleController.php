<?php

namespace Monarul007\LaravelModularSystem\Http\Controllers;

use Illuminate\Routing\Controller;
use Monarul007\LaravelModularSystem\Core\ModuleManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class AdminModuleController extends Controller
{
    public function __construct(
        protected ModuleManager $moduleManager
    ) {}

    public function index(): Response
    {
        $modules = $this->moduleManager->getAllModules();
        
        return Inertia::render('Admin/Modules/Index', [
            'modules' => $modules
        ]);
    }

    public function enable(Request $request)
    {
        $request->validate([
            'name' => 'required|string'
        ]);

        $moduleName = $request->name;

        if (!$this->moduleManager->moduleExists($moduleName)) {
            return back()->withErrors(['name' => "Module '{$moduleName}' does not exist"]);
        }

        try {
            if ($this->moduleManager->enableModule($moduleName)) {
                return back()->with('success', "Module '{$moduleName}' enabled successfully");
            }
        } catch (\Exception $e) {
            return back()->withErrors(['name' => $e->getMessage()]);
        }

        return back()->withErrors(['name' => "Failed to enable module '{$moduleName}'"]);
    }

    public function disable(Request $request)
    {
        $request->validate([
            'name' => 'required|string'
        ]);

        $moduleName = $request->name;

        if (!$this->moduleManager->moduleExists($moduleName)) {
            return back()->withErrors(['name' => "Module '{$moduleName}' does not exist"]);
        }

        try {
            if ($this->moduleManager->disableModule($moduleName)) {
                return back()->with('success', "Module '{$moduleName}' disabled successfully");
            }
        } catch (\Exception $e) {
            return back()->withErrors(['name' => $e->getMessage()]);
        }

        return back()->withErrors(['name' => "Failed to disable module '{$moduleName}'"]);
    }

    public function upload(Request $request)
    {
        try {
            $request->validate([
                'module_zip' => 'required|file|mimes:zip|max:2048', // 2MB max to match PHP settings
                'module_name' => 'nullable|string|max:50'
            ]);

            $zipFile = $request->file('module_zip');
            $moduleName = $request->input('module_name');

            if (!$zipFile || !$zipFile->isValid()) {
                return back()->withErrors(['upload' => 'No valid file was uploaded']);
            }

            // Ensure storage directory exists with proper permissions
            $storageDir = storage_path('app/temp_modules');
            if (!file_exists($storageDir)) {
                if (!mkdir($storageDir, 0755, true)) {
                    return back()->withErrors(['upload' => 'Could not create storage directory']);
                }
            }

            // Check if directory is writable
            if (!is_writable($storageDir)) {
                return back()->withErrors(['upload' => 'Storage directory is not writable']);
            }

            // Generate unique filename to avoid conflicts
            $filename = 'module_' . time() . '_' . uniqid() . '.zip';
            $fullTempPath = $storageDir . DIRECTORY_SEPARATOR . $filename;

            // Move uploaded file manually for better error handling
            if (!$zipFile->move($storageDir, $filename)) {
                return back()->withErrors(['upload' => 'Failed to move uploaded file']);
            }

            if (!file_exists($fullTempPath)) {
                return back()->withErrors(['upload' => 'Uploaded file not found after move']);
            }

            // Verify it's a valid ZIP file
            $zip = new \ZipArchive();
            if ($zip->open($fullTempPath) !== TRUE) {
                unlink($fullTempPath);
                return back()->withErrors(['upload' => 'Invalid ZIP file']);
            }
            $zip->close();

            $result = $this->moduleManager->installModuleFromZip($fullTempPath, $moduleName);
            
            // Clean up temp file
            if (file_exists($fullTempPath)) {
                unlink($fullTempPath);
            }

            if ($result['success']) {
                return back()->with('success', $result['message']);
            } else {
                return back()->withErrors(['upload' => $result['message']]);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors());
        } catch (\Exception $e) {
            // Clean up temp file on error
            if (isset($fullTempPath) && file_exists($fullTempPath)) {
                unlink($fullTempPath);
            }
            
            Log::error('Module upload failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $zipFile ? $zipFile->getClientOriginalName() : 'unknown'
            ]);
            
            return back()->withErrors(['upload' => 'Failed to install module: ' . $e->getMessage()]);
        }
    }

    public function uninstall(Request $request)
    {
        $request->validate([
            'name' => 'required|string'
        ]);

        $moduleName = $request->name;

        if (!$this->moduleManager->moduleExists($moduleName)) {
            return back()->withErrors(['name' => "Module '{$moduleName}' does not exist"]);
        }

        if ($this->moduleManager->uninstallModule($moduleName)) {
            return back()->with('success', "Module '{$moduleName}' uninstalled successfully");
        }

        return back()->withErrors(['name' => "Failed to uninstall module '{$moduleName}'"]);
    }

    public function download(string $name)
    {
        if (!$this->moduleManager->moduleExists($name)) {
            return back()->withErrors(['name' => "Module '{$name}' does not exist"]);
        }

        $zipPath = $this->moduleManager->createModuleZip($name);
        
        if (!$zipPath || !file_exists($zipPath)) {
            return back()->withErrors(['name' => "Failed to create ZIP for module '{$name}'"]);
        }

        return response()->download($zipPath, "{$name}.zip")->deleteFileAfterSend();
    }
}