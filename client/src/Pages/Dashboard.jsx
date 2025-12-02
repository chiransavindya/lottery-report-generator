import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
<<<<<<< HEAD:client/src/Pages/Dashboard.jsx
import '../css/dashboard.css';
=======
import '../../css/dashboard.css';
>>>>>>> 7dadbdd797c315785d0ff54abc7d518c78689039:resources/js/Pages/Dashboard.jsx
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
