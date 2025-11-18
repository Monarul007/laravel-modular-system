import React from 'react';
import { Head } from '@inertiajs/react';
import AdminLayout from './Layout';

export default function Dashboard({ stats, recent_modules }) {
    return (
        <AdminLayout>
            <Head title="Dashboard" />
            
            <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div className="p-6 bg-white border-b border-gray-200">
                    <h2 className="text-2xl font-bold mb-6">Dashboard</h2>
                    
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div className="bg-blue-50 p-6 rounded-lg">
                            <h3 className="text-lg font-semibold text-blue-900">Total Modules</h3>
                            <p className="text-3xl font-bold text-blue-600">{stats.total_modules}</p>
                        </div>
                        
                        <div className="bg-green-50 p-6 rounded-lg">
                            <h3 className="text-lg font-semibold text-green-900">Enabled Modules</h3>
                            <p className="text-3xl font-bold text-green-600">{stats.enabled_modules}</p>
                        </div>
                        
                        <div className="bg-gray-50 p-6 rounded-lg">
                            <h3 className="text-lg font-semibold text-gray-900">Disabled Modules</h3>
                            <p className="text-3xl font-bold text-gray-600">{stats.disabled_modules}</p>
                        </div>
                    </div>

                    {recent_modules && recent_modules.length > 0 && (
                        <div>
                            <h3 className="text-xl font-bold mb-4">Recent Modules</h3>
                            <div className="space-y-3">
                                {recent_modules.map((module) => (
                                    <div key={module.name} className="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                        <div>
                                            <h4 className="font-semibold">{module.name}</h4>
                                            <p className="text-sm text-gray-600">{module.description || 'No description'}</p>
                                        </div>
                                        <span className={`px-3 py-1 text-sm rounded-full ${
                                            module.enabled 
                                                ? 'bg-green-100 text-green-800' 
                                                : 'bg-gray-100 text-gray-800'
                                        }`}>
                                            {module.enabled ? 'Enabled' : 'Disabled'}
                                        </span>
                                    </div>
                                ))}
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </AdminLayout>
    );
}
