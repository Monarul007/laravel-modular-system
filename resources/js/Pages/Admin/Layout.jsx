import React from 'react';
import { Link, usePage } from '@inertiajs/react';

export default function AdminLayout({ children }) {
    const { url } = usePage();
    const { flash } = usePage().props;

    const isActive = (path) => {
        return url.startsWith(path);
    };

    return (
        <div className="min-h-screen bg-gray-100">
            {/* Navigation */}
            <nav className="bg-white border-b border-gray-200">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="flex justify-between h-16">
                        <div className="flex">
                            <div className="flex-shrink-0 flex items-center">
                                <h1 className="text-xl font-bold">Admin Panel</h1>
                            </div>
                            <div className="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                                <Link
                                    href="/admin"
                                    className={`inline-flex items-center px-1 pt-1 border-b-2 ${
                                        isActive('/admin') && url === '/admin'
                                            ? 'border-indigo-500'
                                            : 'border-transparent'
                                    } text-sm font-medium`}
                                >
                                    Dashboard
                                </Link>
                                <Link
                                    href="/admin/modules"
                                    className={`inline-flex items-center px-1 pt-1 border-b-2 ${
                                        isActive('/admin/modules')
                                            ? 'border-indigo-500'
                                            : 'border-transparent'
                                    } text-sm font-medium`}
                                >
                                    Modules
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            {/* Page Content */}
            <main className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    {/* Flash Messages */}
                    {flash?.success && (
                        <div className="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                            {flash.success}
                        </div>
                    )}

                    {flash?.error && (
                        <div className="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            {flash.error}
                        </div>
                    )}

                    {children}
                </div>
            </main>
        </div>
    );
}
