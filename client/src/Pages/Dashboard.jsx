import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import '../css/dashboard.css';
import Upload from '../Components/Upload';

export default function Dashboard(props) {
    return (
        <AuthenticatedLayout
            auth={props.auth}
            errors={props.errors}
        >
            <Head title="Dashboard" />

            <div className="dashboard-container">
                <div className="upload-container">
                    <Upload />
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
