import BlueprintGenerator from '@/Components/BlueprintGenerator';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import GuestLayout from '@/Layouts/GuestLayout';
import { PageProps } from '@/types';
import { Head } from '@inertiajs/react';

interface GeneratorProps extends PageProps {
    phpVersions: string[];
    wordpressVersions: string[];
    data?: {
        id?: string;
        name?: string;
        status?: string;
        php_version?: string;
        wordpress_version?: string;
        steps?: Array<{
            step: string;
            username?: string;
            password?: string;
            plugin?: string;
        }>;
        is_anonymous?: boolean;
    };
}

export default function Generator({
    auth,
    phpVersions,
    wordpressVersions,
    data,
}: GeneratorProps) {
    const Layout = auth.user ? AuthenticatedLayout : GuestLayout;

    return (
        <Layout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Blueprint Generator
                </h2>
            }
        >
            <Head title="Blueprint Generator" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <BlueprintGenerator
                        phpVersions={phpVersions}
                        wordpressVersions={wordpressVersions}
                        initialData={data}
                    />
                </div>
            </div>
        </Layout>
    );
}
