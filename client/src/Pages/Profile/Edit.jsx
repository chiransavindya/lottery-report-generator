import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
<<<<<<< HEAD:client/src/Pages/Profile/Edit.jsx
import '../../css/profile.css';
=======
import '../../../css/profile.css';
>>>>>>> 7dadbdd797c315785d0ff54abc7d518c78689039:resources/js/Pages/Profile/Edit.jsx
import DeleteUserForm from './Partials/DeleteUserForm';
import UpdatePasswordForm from './Partials/UpdatePasswordForm';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm';

export default function Edit({ auth, mustVerifyEmail, status }) {
    return (
        <AuthenticatedLayout
            auth={auth}
        >
            <Head title="Profile" />

            <div className="profile-page">
                <div className="profile-grid">
                    <div className="profile-card">
                        <UpdateProfileInformationForm
                            mustVerifyEmail={mustVerifyEmail}
                            status={status}
                            className="max-w-xl"
                        />
                    </div>

                    <div className="profile-card">
                        <UpdatePasswordForm className="max-w-xl" />
                    </div>

                    <div className="profile-card">
                        <DeleteUserForm className="max-w-xl" />
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
