import { Navigate, Route, Routes } from 'react-router-dom';
import { Layout } from './components/Layout';
import { useAuth } from './lib/useAuth';
import { AttendancePage } from './pages/AttendancePage';
import { DashboardPage } from './pages/DashboardPage';
import { LoginPage } from './pages/LoginPage';
import { MembersPage } from './pages/MembersPage';
import { ReportsPage } from './pages/ReportsPage';
import { UsersPage } from './pages/UsersPage';

const ProtectedRoutes = () => {
  const { user } = useAuth();
  if (!user) {
    return <Navigate to="/login" replace />;
  }

  return (
    <Routes>
      <Route element={<Layout />}>
        <Route index element={<DashboardPage />} />
        <Route path="/jemaat" element={<MembersPage />} />
        <Route path="/kehadiran" element={<AttendancePage />} />
        <Route path="/users" element={<UsersPage />} />
        <Route path="/laporan" element={<ReportsPage />} />
      </Route>
    </Routes>
  );
};

function App() {
  const { user } = useAuth();

  return <>{user ? <ProtectedRoutes /> : <LoginPage />}</>;
}

export default App;
