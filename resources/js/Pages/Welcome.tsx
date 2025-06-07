import BlueprintCard from '@/Components/BlueprintCard';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import GuestLayout from '@/Layouts/GuestLayout';
import { PageProps } from '@/types';
import { Head } from '@inertiajs/react';

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

export default function Welcome({
    auth,
    blueprints,
}: PageProps<{
    blueprints: Blueprint[];
}>) {
    const content = (
        <>
            <Head title="Welcome" />
            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="mb-8">
                        <h2 className="mb-6 text-2xl font-bold text-black dark:text-white">
                            Latest Blueprints
                        </h2>
                        {blueprints.length === 0 ? (
                            <div className="py-12 text-center">
                                <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                                    <div className="p-6 text-gray-900 dark:text-gray-100">
                                        <div className="mb-4 text-lg font-medium">
                                            No blueprints available
                                        </div>
                                        <p className="mb-6 text-gray-600 dark:text-gray-400">
                                            Check back later for new WordPress
                                            blueprints
                                        </p>
                                    </div>
                                </div>
                            </div>
                        ) : (
                            <div className="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                                {blueprints.map((blueprint) => (
                                    <BlueprintCard
                                        key={blueprint.id}
                                        blueprint={blueprint}
                                        showActions={false}
                                        showStatus={false}
                                    />
                                ))}
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </>
    );

    // Use AuthenticatedLayout if user is logged in, otherwise use GuestLayout
    if (auth.user) {
        return (
            <AuthenticatedLayout
                header={
                    <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                        Welcome
                    </h2>
                }
            >
                {content}
            </AuthenticatedLayout>
        );
    }

    return <GuestLayout>{content}</GuestLayout>;
}
