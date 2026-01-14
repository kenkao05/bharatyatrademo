// auth-helper.js - Shared Authentication Helper
// Save this as: js/auth-helper.js

import { createClient } from 'https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2/+esm';

// ⚠️ REPLACE WITH YOUR SUPABASE CREDENTIALS
const SUPABASE_URL = 'https://hmobboiupblojankdaah.supabase.co';
const SUPABASE_KEY = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Imhtb2Jib2l1cGJsb2phbmtkYWFoIiwicm9sZSI6ImFub24iLCJpYXQiOjE3Njc3NjQ1MjUsImV4cCI6MjA4MzM0MDUyNX0.LUJiZUbu-BZQRg8LWhcgnjILBFqZimaGbYBrv6BDVYE';

// Initialize Supabase client
export const supabase = createClient(SUPABASE_URL, SUPABASE_KEY);

/**
 * Check if user is authenticated
 * Returns user object if authenticated, null otherwise
 */
export async function checkAuth() {
  try {
    const { data: { user }, error } = await supabase.auth.getUser();
    
    if (error) {
      console.error('Auth check error:', error);
      return null;
    }
    
    return user;
  } catch (err) {
    console.error('Auth check failed:', err);
    return null;
  }
}

/**
 * Require authentication - redirects to login if not authenticated
 * Call this at the start of protected pages
 */
export async function requireAuth() {
  const user = await checkAuth();
  
  if (!user) {
    // Not authenticated - redirect to login
    console.log('User not authenticated, redirecting to login...');
    window.location.href = '/admin-login.html';
    return null;
  }
  
  return user;
}

/**
 * Get current user info
 * Returns user object or null
 */
export async function getCurrentUser() {
  return await checkAuth();
}

/**
 * Logout user and redirect to login page
 */
export async function logout() {
  try {
    const { error } = await supabase.auth.signOut();
    
    if (error) {
      console.error('Logout error:', error);
      throw error;
    }
    
    console.log('Logged out successfully');
    window.location.href = '/admin-login.html';
  } catch (err) {
    console.error('Logout failed:', err);
    // Force redirect anyway
    window.location.href = '/admin-login.html';
  }
}

/**
 * Login with email and password
 * Returns { user, error }
 */
export async function login(email, password) {
  try {
    const { data, error } = await supabase.auth.signInWithPassword({
      email,
      password
    });
    
    if (error) {
      return { user: null, error: error.message };
    }
    
    return { user: data.user, error: null };
  } catch (err) {
    return { user: null, error: err.message };
  }
}

/**
 * Check if user is on login page and already authenticated
 * If yes, redirect to dashboard
 */
export async function redirectIfAuthenticated() {
  const user = await checkAuth();
  
  if (user) {
    console.log('User already authenticated, redirecting to dashboard...');
    window.location.href = '/admin-dashboard.html';
  }
}

// Export supabase client for direct use if needed
export { SUPABASE_URL, SUPABASE_KEY };