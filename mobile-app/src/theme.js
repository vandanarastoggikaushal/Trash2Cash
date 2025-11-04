import { DefaultTheme } from 'react-native-paper';

export const theme = {
  ...DefaultTheme,
  colors: {
    ...DefaultTheme.colors,
    primary: '#15803d',
    secondary: '#22c55e',
    accent: '#f59e0b',
    background: '#ffffff',
    surface: '#f9fafb',
    text: '#1f2937',
    disabled: '#9ca3af',
    placeholder: '#6b7280',
    backdrop: 'rgba(0, 0, 0, 0.5)',
  },
};

export const colors = {
  brand: '#15803d',
  brandLight: '#22c55e',
  brandDark: '#166534',
  accent: '#f59e0b',
  success: '#10b981',
  error: '#ef4444',
  warning: '#f59e0b',
  info: '#3b82f6',
};

// Export default theme for PaperProvider
export default theme;

