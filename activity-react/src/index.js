import React from 'react';
import ReactDOM from 'react-dom/client';
import { BrowserRouter, Routes, Route, Navigate, Outlet } from 'react-router-dom';
import { AuthProvider, useAuth } from './api/AuthContext';
import Login from './components/Authentication/Login';
import Register from './components/Authentication/Register';
import Layout from './components/Layout';
import Welcome from './components/Welcome';
import Products from './components/Products';
import CreateProductForm from './components/Products/CreateProductForm';
import AboutMe from './components/AboutMe';
import './index.css';

const ProtectedRoute = () => {
  const { user, loading } = useAuth();

  if (loading) {
    return (
      <div style={styles.center}>
        <p style={{ color: '#6b7280' }}>Verifying secure session...</p>
      </div>
    );
  }

  // Redirect to login safely if context state has no valid user payload
  return user ? <Outlet /> : <Navigate to="/login" replace />;
};

const App = () => {
  return (
    <AuthProvider>
      <BrowserRouter>
        <Routes>
          {/* Public routes */}
          <Route path="/login" element={<Login />} />
          <Route path="/register" element={<Register />} />

          {/* Protected routes: session check first, then the shared navbar layout */}
          <Route element={<ProtectedRoute />}>
            <Route element={<Layout />}>
              <Route path="/welcome" element={<Welcome />} />
              <Route path="/product" element={<Products />} />
              <Route path="/product/create" element={<CreateProductForm />} />
              <Route path="/about" element={<AboutMe />} />
            </Route>
          </Route>

          <Route path="*" element={<Navigate to="/login" replace />} />
        </Routes>
      </BrowserRouter>
    </AuthProvider>
  );
};

const styles = {
  center: { display: 'flex', justifyContent: 'center', alignItems: 'center', minHeight: '100vh', fontFamily: 'sans-serif' },
};

const root = ReactDOM.createRoot(document.getElementById('root'));
root.render(
  <React.StrictMode>
    <App />
  </React.StrictMode>
);
