import React, { createContext, useEffect, useState, useMemo } from 'react';
import AsyncStorage from '@react-native-async-storage/async-storage';
import {
  loginUser,
  registerUser,
  getProfile,
  updateProfile as updateProfileRequest,
  logoutUser,
} from '../services/apiAuth';

export const AuthContext = createContext({
  user: null,
  authToken: null,
  isAuthenticated: false,
  loading: false,
  signIn: async () => {},
  signUp: async () => {},
  signOut: async () => {},
  refreshProfile: async () => {},
  updateProfile: async () => {},
});

const TOKEN_STORAGE_KEY = 'trash2cash_auth_token';

export function AuthProvider({ children }) {
  const [authToken, setAuthToken] = useState(null);
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);
  const [authError, setAuthError] = useState(null);

  useEffect(() => {
    (async () => {
      try {
        const storedToken = await AsyncStorage.getItem(TOKEN_STORAGE_KEY);
        if (storedToken) {
          setAuthToken(storedToken);
          const profile = await getProfile(storedToken);
          setUser(profile.user);
        }
      } catch (error) {
        console.error('[Auth] Failed to bootstrap auth state', error);
      } finally {
        setLoading(false);
      }
    })();
  }, []);

  const handleAuthSuccess = async (token, profile) => {
    setAuthToken(token);
    setUser(profile.user);
    setAuthError(null);
    await AsyncStorage.setItem(TOKEN_STORAGE_KEY, token);
  };

  const signIn = async (username, password) => {
    setLoading(true);
    setAuthError(null);
    try {
      const profile = await loginUser(username, password);
      await handleAuthSuccess(profile.token, profile);
      return { success: true };
    } catch (error) {
      console.error('[Auth] login error', error);
      setAuthError(error.message || 'Failed to login');
      return { success: false, error: error.message };
    } finally {
      setLoading(false);
    }
  };

  const signUp = async (registrationData) => {
    setLoading(true);
    setAuthError(null);
    try {
      const profile = await registerUser(registrationData);
      await handleAuthSuccess(profile.token, profile);
      return { success: true };
    } catch (error) {
      console.error('[Auth] registration error', error);
      setAuthError(error.message || 'Failed to register');
      return { success: false, error: error.message };
    } finally {
      setLoading(false);
    }
  };

  const signOut = async () => {
    try {
      if (authToken) {
        await logoutUser(authToken);
      }
    } catch (error) {
      console.warn('[Auth] logout warning', error);
    } finally {
      setAuthToken(null);
      setUser(null);
      setAuthError(null);
      await AsyncStorage.removeItem(TOKEN_STORAGE_KEY);
    }
  };

  const refreshProfile = async () => {
    if (!authToken) return;
    try {
      const profile = await getProfile(authToken);
      setUser(profile.user);
    } catch (error) {
      console.error('[Auth] refresh profile error', error);
      if (error?.status === 401) {
        await signOut();
      }
    }
  };

  const updateProfile = async (updates) => {
    if (!authToken) {
      throw new Error('Not authenticated');
    }
    setLoading(true);
    try {
      const profile = await updateProfileRequest(authToken, updates);
      setUser(profile.user);
      setAuthError(null);
      return { success: true };
    } catch (error) {
      console.error('[Auth] update profile error', error);
      setAuthError(error.message || 'Failed to update profile');
      return { success: false, error: error.message };
    } finally {
      setLoading(false);
    }
  };

  const value = useMemo(() => ({
    user,
    authToken,
    isAuthenticated: !!authToken && !!user,
    loading,
    authError,
    signIn,
    signUp,
    signOut,
    refreshProfile,
    updateProfile,
    clearError: () => setAuthError(null),
  }), [user, authToken, loading, authError]);

  return (
    <AuthContext.Provider value={value}>
      {children}
    </AuthContext.Provider>
  );
}

