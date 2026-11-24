import React from 'react';
import { NavLink, useNavigate } from 'react-router-dom';
import { useAuth } from '../api/AuthContext';

export default function Navbar() {
  const { user, logout } = useAuth();
  const navigate = useNavigate();

  const handleLogout = async () => {
    await logout();
    navigate('/login');
  };

  // NavLink passes { isActive } so the current page can be highlighted
  const linkStyle = ({ isActive }) => ({
    color: isActive ? '#2563eb' : '#374151',
    backgroundColor: isActive ? '#eff6ff' : 'transparent',
    fontWeight: isActive ? '600' : '500',
    textDecoration: 'none',
    padding: '8px 12px',
    borderRadius: '6px',
  });

  return (
    <nav style={styles.nav}>
      <div style={styles.inner}>
        <span style={styles.brand}>Laravel + React</span>
        <div style={styles.links}>
          <NavLink to="/welcome" style={linkStyle}>Home</NavLink>
          {/* "end" keeps this link from staying active on /product/create */}
          <NavLink to="/product" end style={linkStyle}>Product Catalog</NavLink>
          <NavLink to="/product/create" style={linkStyle}>Add Product</NavLink>
          <NavLink to="/about" style={linkStyle}>About Me</NavLink>
        </div>
        <div style={styles.userBox}>
          <span style={styles.userName}>{user?.name}</span>
          <button onClick={handleLogout} style={styles.logoutBtn}>Logout</button>
        </div>
      </div>
    </nav>
  );
}

const styles = {
  nav: { backgroundColor: '#ffffff', borderBottom: '1px solid #e5e7eb', position: 'sticky', top: 0, zIndex: 10 },
  inner: { maxWidth: '1200px', margin: '0 auto', padding: '12px 20px', display: 'flex', alignItems: 'center', justifyContent: 'space-between', flexWrap: 'wrap', gap: '12px' },
  brand: { fontSize: '18px', fontWeight: '700', color: '#111827' },
  links: { display: 'flex', alignItems: 'center', gap: '4px', flexWrap: 'wrap' },
  userBox: { display: 'flex', alignItems: 'center', gap: '12px' },
  userName: { fontSize: '14px', color: '#4b5563' },
  logoutBtn: { padding: '8px 14px', border: '1px solid #d1d5db', borderRadius: '6px', backgroundColor: '#fff', cursor: 'pointer', fontWeight: '500' },
};
