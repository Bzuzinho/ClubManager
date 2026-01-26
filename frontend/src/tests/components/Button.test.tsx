import { render, screen, fireEvent } from '@testing-library/react';
import { describe, it, expect, vi } from 'vitest';

// Mock Button component
const Button = ({ 
  children, 
  onClick, 
  variant = 'primary',
  disabled = false,
  type = 'button',
}: {
  children: React.ReactNode;
  onClick?: () => void;
  variant?: 'primary' | 'secondary' | 'danger';
  disabled?: boolean;
  type?: 'button' | 'submit' | 'reset';
}) => (
  <button
    type={type}
    onClick={onClick}
    disabled={disabled}
    className={`btn btn-${variant}`}
    data-testid="button"
  >
    {children}
  </button>
);

describe('Button Component', () => {
  it('renders children correctly', () => {
    render(<Button>Click me</Button>);
    expect(screen.getByText('Click me')).toBeInTheDocument();
  });

  it('calls onClick when clicked', () => {
    const handleClick = vi.fn();
    render(<Button onClick={handleClick}>Click me</Button>);
    
    fireEvent.click(screen.getByTestId('button'));
    
    expect(handleClick).toHaveBeenCalledTimes(1);
  });

  it('does not call onClick when disabled', () => {
    const handleClick = vi.fn();
    render(<Button onClick={handleClick} disabled>Click me</Button>);
    
    const button = screen.getByTestId('button');
    expect(button).toBeDisabled();
    
    fireEvent.click(button);
    
    expect(handleClick).not.toHaveBeenCalled();
  });

  it('applies correct variant class', () => {
    render(<Button variant="danger">Delete</Button>);
    
    const button = screen.getByTestId('button');
    expect(button).toHaveClass('btn-danger');
  });

  it('defaults to primary variant', () => {
    render(<Button>Submit</Button>);
    
    const button = screen.getByTestId('button');
    expect(button).toHaveClass('btn-primary');
  });

  it('renders with correct type attribute', () => {
    render(<Button type="submit">Submit</Button>);
    
    const button = screen.getByTestId('button');
    expect(button).toHaveAttribute('type', 'submit');
  });
});
