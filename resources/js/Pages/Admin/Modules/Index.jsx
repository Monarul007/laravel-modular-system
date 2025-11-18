import React, { useState } from 'react';
import { Head, router, useForm } from '@inertiajs/react';
import AdminLayout from '../Layout';

export default function ModulesIndex({ modules }) {
    const [showUploadModal, setShowUploadModal] = useState(false);
    const { data, setData, post, processing, errors, reset } = useForm({
        module_zip: null,
        module_name: '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/admin/modules/upload', {
            onSuccess: () => {
                reset();
                setShowUploadModal(false);
            },
        });
    };

    const handleAction = (action, moduleName) => {
        if (action === 'uninstall') {
            if (!confirm(`Are you sure you want to uninstall ${moduleName}?`)) {
                return;
            }
        }
        
        router.post(`/admin/modules/${action}`, { name: moduleName });
    };

    return (
        <AdminLayout>
            <Head title="Modules" />
            
            <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div className="p-6 bg-white border-b border-gray-200">
                    <div className="flex justify-between items-center mb-6">
                        <h2 className="text-2xl font-bold">Modules</h2>
                        <button
                            onClick={() => setShowUploadModal(true)}
                            className="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md"
                        >
                            Upload Module
                        </button>
                    </div>

                    {modules && modules.length > 0 ? (
                        <div className="space-y-4">
                            {modules.map((module) => (
                                <div key={module.name} className="border border-gray-200 rounded-lg p-6">
                                    <div className="flex items-start justify-between">
                                        <div className="flex-1">
                                            <h3 className="text-lg font-semibold">{module.name}</h3>
                                            <p className="text-gray-600 mt-1">{module.description || 'No description'}</p>
                                            <div className="mt-2 flex items-center space-x-4 text-sm text-gray-500">
                                                <span>Version: {module.version || 'N/A'}</span>
                                                {module.author && <span>Author: {module.author}</span>}
                                            </div>
                                        </div>
                                        
                                        <div className="flex items-center space-x-2">
                                            <span className={`px-3 py-1 text-sm rounded-full ${
                                                module.enabled 
                                                    ? 'bg-green-100 text-green-800' 
                                                    : 'bg-gray-100 text-gray-800'
                                            }`}>
                                                {module.enabled ? 'Enabled' : 'Disabled'}
                                            </span>
                                            
                                            <button
                                                onClick={() => handleAction(module.enabled ? 'disable' : 'enable', module.name)}
                                                className={`px-3 py-1 text-sm rounded ${
                                                    module.enabled
                                                        ? 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200'
                                                        : 'bg-green-100 text-green-800 hover:bg-green-200'
                                                }`}
                                            >
                                                {module.enabled ? 'Disable' : 'Enable'}
                                            </button>
                                            
                                            <a
                                                href={`/admin/modules/download/${module.name}`}
                                                className="px-3 py-1 text-sm rounded bg-blue-100 text-blue-800 hover:bg-blue-200"
                                            >
                                                Download
                                            </a>
                                            
                                            <button
                                                onClick={() => handleAction('uninstall', module.name)}
                                                className="px-3 py-1 text-sm rounded bg-red-100 text-red-800 hover:bg-red-200"
                                            >
                                                Uninstall
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            ))}
                        </div>
                    ) : (
                        <div className="text-center py-12 text-gray-500">
                            <p>No modules installed yet.</p>
                            <p className="mt-2">Upload a module to get started.</p>
                        </div>
                    )}
                </div>
            </div>

            {/* Upload Modal */}
            {showUploadModal && (
                <div className="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                    <div className="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                        <div className="flex justify-between items-center mb-4">
                            <h3 className="text-lg font-semibold">Upload Module</h3>
                            <button
                                onClick={() => setShowUploadModal(false)}
                                className="text-gray-400 hover:text-gray-600"
                            >
                                <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        
                        <form onSubmit={handleSubmit}>
                            <div className="mb-4">
                                <label className="block text-sm font-medium text-gray-700 mb-2">
                                    Module ZIP File
                                </label>
                                <input
                                    type="file"
                                    accept=".zip"
                                    onChange={(e) => setData('module_zip', e.target.files[0])}
                                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                    required
                                />
                                {errors.module_zip && (
                                    <p className="mt-1 text-sm text-red-600">{errors.module_zip}</p>
                                )}
                            </div>
                            
                            <div className="mb-4">
                                <label className="block text-sm font-medium text-gray-700 mb-2">
                                    Module Name (Optional)
                                </label>
                                <input
                                    type="text"
                                    value={data.module_name}
                                    onChange={(e) => setData('module_name', e.target.value)}
                                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="Leave empty to auto-detect"
                                />
                            </div>
                            
                            <div className="flex justify-end space-x-2">
                                <button
                                    type="button"
                                    onClick={() => setShowUploadModal(false)}
                                    className="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 disabled:opacity-50"
                                >
                                    {processing ? 'Uploading...' : 'Upload'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </AdminLayout>
    );
}
