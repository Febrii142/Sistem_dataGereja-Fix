import { render, screen } from '@testing-library/react';
import { BrowserRouter } from 'react-router-dom';
import App from '../App';
import { AuthProvider } from '../lib/auth';
import { beforeEach, describe, expect, it } from 'vitest';

describe('App', () => {
  beforeEach(() => {
    localStorage.clear();
  });

  it('shows login page when unauthenticated', () => {
    render(
      <BrowserRouter>
        <AuthProvider>
          <App />
        </AuthProvider>
      </BrowserRouter>,
    );

    expect(screen.getByText('Login Sistem Informasi Jemaat')).toBeInTheDocument();
  });
});
