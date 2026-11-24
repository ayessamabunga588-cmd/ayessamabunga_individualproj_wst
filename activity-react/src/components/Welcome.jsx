import React from 'react';
import { Link } from 'react-router-dom';
import { useAuth } from '../api/AuthContext';

export default function Welcome() {
  const { user } = useAuth();

  return (
    <div style={{ maxWidth: '800px', margin: '60px auto', padding: '0 20px', textAlign: 'center' }}>
      <h1 style={{ fontSize: '32px', color: '#111827' }}>Welcome, {user?.name}!</h1>
      <p style={{ color: '#6b7280', fontSize: '18px' }}>
        You are signed in with a secure cookie-based session.
      </p>
      <div style={{ display: 'flex', justifyContent: 'center', gap: '12px', marginTop: '24px' }}>
        <Link to="/product" style={{ backgroundColor: '#2563eb', color: '#fff', padding: '12px 20px', borderRadius: '6px', textDecoration: 'none' }}>
          Browse Products
        </Link>
        <Link to="/about" style={{ border: '1px solid #d1d5db', color: '#374151', padding: '12px 20px', borderRadius: '6px', textDecoration: 'none' }}>
          About Me
        </Link>
      </div>
    </div>
  );
}
