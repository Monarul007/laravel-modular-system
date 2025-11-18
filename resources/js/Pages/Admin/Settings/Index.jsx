import React from 'react';
import { Head, useForm } from '@inertiajs/react';
import AdminLayout from '../Layout';

export default function SettingsIndex({ settings }) {
    return (
        <AdminLayout>
            <Head title="Settings" />
            
            <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div className="p-6 bg-white border-b border-gray-200">
                    <h2 className="text-2xl font-bold mb-6">Settings</h2>
                    
                    <div className="space-y-6">
                        {Object.entries(settings).map(([group, groupSettings]) => (
                            <SettingsGroup key={group} group={group} settings={groupSettings} />
                        ))}
                    </div>
                </div>
            </div>
        </AdminLayout>
    );
}

function SettingsGroup({ group, settings }) {
    const { data, setData, post, processing } = useForm(settings);

    const handleSubmit = (e) => {
        e.preventDefault();
        post(`/admin/settings/${group}`);
    };

    const renderInput = (key, value) => {
        const label = key.replace(`${group}.`, '').replace(/_/g, ' ');
        
        if (typeof value === 'boolean') {
            return (
                <select
                    value={data[key] ? '1' : '0'}
                    onChange={(e) => setData(key, e.target.value === '1')}
                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                >
                    <option value="1">Enabled</option>
                    <option value="0">Disabled</option>
                </select>
            );
        } else if (typeof value === 'number') {
            return (
                <input
                    type="number"
                    value={data[key]}
                    onChange={(e) => setData(key, parseFloat(e.target.value))}
                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                />
            );
        } else {
            return (
                <input
                    type="text"
                    value={data[key]}
                    onChange={(e) => setData(key, e.target.value)}
                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                />
            );
        }
    };

    return (
        <div className="border border-gray-200 rounded-lg p-6">
            <h3 className="text-lg font-semibold mb-4 capitalize">{group} Settings</h3>
            
            <form onSubmit={handleSubmit}>
                {Object.keys(settings).length > 0 ? (
                    <>
                        <div className="space-y-4">
                            {Object.entries(settings).map(([key, value]) => (
                                <div key={key}>
                                    <label className="block text-sm font-medium text-gray-700 mb-2 capitalize">
                                        {key.replace(`${group}.`, '').replace(/_/g, ' ')}
                                    </label>
                                    {renderInput(key, value)}
                                </div>
                            ))}
                        </div>
                        
                        <div className="mt-6">
                            <button
                                type="submit"
                                disabled={processing}
                                className="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 disabled:opacity-50"
                            >
                                {processing ? 'Saving...' : `Save ${group.charAt(0).toUpperCase() + group.slice(1)} Settings`}
                            </button>
                        </div>
                    </>
                ) : (
                    <p className="text-gray-500">No settings available for this group.</p>
                )}
            </form>
        </div>
    );
}
