import React, { createContext, useContext, useState, useEffect } from 'react';
import axios from 'axios';
import api, { API_ORIGIN } from './axios';

const AuthContext = createContext(null);

export const AuthProvider = ({ children }) => {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const initializeAuth = async () => {
      try {
        const response = await api.get('auth/me');
        setUser(response.data.data);
      } catch (err) {
        setUser(null);
      } finally {
        setLoading(false);
      }
    };

    initializeAuth();
  }, []);

  const login = async (email, password) => {
    // Sanctum CSRF handshake challenge trigger
    await axios.get(`${API_ORIGIN}/sanctum/csrf-cookie`, { withCredentials: true });

    const response = await api.post('auth/login', { email, password });
    setUser(response.data.data);
  };

  const register = async (name, email, password, passwordConfirmation) => {
    // Sanctum CSRF handshake challenge trigger
    await axios.get(`${API_ORIGIN}/sanctum/csrf-cookie`, { withCredentials: true });

    const response = await api.post('auth/register', {
      name,
      email,
      password,
      password_confirmation: passwordConfirmation,
    });
    setUser(response.data.data);
  };

  const logout = async () => {
    try {
      await api.post('auth/logout');
    } finally {
      setUser(null);
    }
  };

  return (
    <AuthContext.Provider value={{ user, login, register, logout, loading }}>
      {children}
    </AuthContext.Provider>
  );
};

export const useAuth = () => useContext(AuthContext);
