import React, { useState } from 'react';
import { useNavigate, Navigate, Link } from 'react-router-dom';
import { useAuth } from '../../api/AuthContext';

export default function Register() {
  const navigate = useNavigate();
  const { register, user } = useAuth();
  const [form, setForm] = useState({ name: '', email: '', password: '', password_confirmation: '' });
  const [error, setError] = useState('');
  const [isSubmitting, setIsSubmitting] = useState(false);

  // Already signed in (including right after a successful registration)
  if (user) {
    return <Navigate to="/welcome" replace />;
  }

  const handleChange = (e) => setForm({ ...form, [e.target.name]: e.target.value });

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError('');
    setIsSubmitting(true);
    try {
      await register(form.name, form.email, form.password, form.password_confirmation);
      navigate('/welcome');
    } catch (err) {
      // Laravel returns validation errors as { errors: { field: [messages] } }
      const responseData = err?.response?.data;
      const firstFieldError = responseData?.errors ? Object.values(responseData.errors)[0]?.[0] : null;
      setError(firstFieldError || responseData?.message || err?.message || 'Unable to register. Please try again.');
    } finally {
      setIsSubmitting(false);
    }
  };

  const inputStyle = { padding: '10px', borderRadius: '6px', border: '1px solid #d1d5db' };

  return (
    <div style={{ display: 'flex', justifyContent: 'center', alignItems: 'center', minHeight: '100vh', backgroundColor: '#f3f4f6' }}>
      <div style={{ backgroundColor: '#ffffff', padding: '32px', borderRadius: '12px', boxShadow: '0 4px 6px rgba(0,0,0,0.1)', width: '100%', maxWidth: '400px' }}>
        <h2>Create Account</h2>
        {error && <div style={{ backgroundColor: '#fee2e2', color: '#991b1b', padding: '12px', borderRadius: '6px', marginBottom: '16px' }}>{error}</div>}
        <form onSubmit={handleSubmit} style={{ display: 'flex', flexDirection: 'column', gap: '16px' }}>
          <input type="text" name="name" placeholder="Full Name" value={form.name} onChange={handleChange} required style={inputStyle} />
          <input type="email" name="email" placeholder="Email Address" value={form.email} onChange={handleChange} required style={inputStyle} />
          <input type="password" name="password" placeholder="Password (min. 8 characters)" value={form.password} onChange={handleChange} required style={inputStyle} />
          <input type="password" name="password_confirmation" placeholder="Confirm Password" value={form.password_confirmation} onChange={handleChange} required style={inputStyle} />
          <button type="submit" disabled={isSubmitting} style={{ backgroundColor: '#2563eb', color: '#fff', border: 'none', padding: '12px', borderRadius: '6px', cursor: 'pointer' }}>
            {isSubmitting ? 'Creating account...' : 'Register'}
          </button>
        </form>
        <p style={{ marginTop: '16px', fontSize: '14px', color: '#6b7280', textAlign: 'center' }}>
          Already have an account? <Link to="/login">Sign in</Link>
        </p>
      </div>
    </div>
  );
}
