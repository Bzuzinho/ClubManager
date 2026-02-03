#!/bin/bash
set -e

# Inicia o backend Laravel
cd backend
php artisan serve --host=0.0.0.0 --port=8000 &
BACKEND_PID=$!
cd ..

# Inicia o frontend (Vite)
cd frontend
npm run dev -- --host 0.0.0.0 --port 5173 &
FRONTEND_PID=$!

# Espera ambos terminarem
wait $BACKEND_PID $FRONTEND_PID
