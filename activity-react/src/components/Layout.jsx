import React from 'react';
import { Outlet } from 'react-router-dom';
import Navbar from './Navbar';

// Wraps every protected page: navbar on top, the active page rendered in <Outlet />
export default function Layout() {
  return (
    <div style={{ minHeight: '100vh', backgroundColor: '#f9fafb', fontFamily: 'system-ui, sans-serif' }}>
      <Navbar />
      <main>
        <Outlet />
      </main>
    </div>
  );
}
