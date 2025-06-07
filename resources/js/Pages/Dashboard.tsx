import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import BlueprintCard from '@/Components/BlueprintCard';

interface Blueprint {
    id: string;
    name: string;
    description?: string;
    status: 'public' | 'private';
    php_version: string;
    wordpress_version: string;
    steps: unknown[];
    created_at: string;
    updated_at: string;
    is_anonymous: boolean;
    statistics?: {
        views_count: number;
        runs_count: number;
        last_viewed_at?: string;
        last_run_at?: string;
    };
}

interface DashboardProps {
    blueprints: Blueprint[];
}

export default function Dashboard({ blueprints }: DashboardProps) {
    return (
        <AuthenticatedLayout
            header={
                <div className="flex justify-between items-center">
                    <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                        My Blueprints
                    </h2>
                    <Link
                        href={route('generator')}
                        className="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        Create New Blueprint
                    </Link>
                </div>
            }
        >
            <Head title="Dashboard" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    {blueprints.length === 0 ? (
                        <div className="text-center py-12">
                            <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg dark:bg-gray-800">
                                <div className="p-6 text-gray-900 dark:text-gray-100">
                                    <div className="text-lg font-medium mb-4">No blueprints yet</div>
                                    <p className="text-gray-600 dark:text-gray-400 mb-6">
                                        Get started by creating your first WordPress blueprint
                                    </p>
                                    <Link
                                        href={route('generator')}
                                        className="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                    >
                                        Create Blueprint
                                    </Link>
                                </div>
                            </div>
                        </div>
                    ) : (
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            {blueprints.map((blueprint) => (
                                <BlueprintCard
                                    key={blueprint.id}
                                    blueprint={blueprint}
                                    showActions={true}
                                />
                            ))}
                        </div>
                    )}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
