# Build stage
FROM node:20-alpine AS builder

ARG VITE_API_URL=http://localhost/api/v1
ENV VITE_API_URL=${VITE_API_URL}

WORKDIR /app

# Copy frontend package files
COPY frontend/package*.json ./
RUN npm ci --prefer-offline

# Copy frontend source
COPY frontend/ ./

# Build frontend
RUN npm run build


# Production stage - serve with Nginx
FROM nginx:alpine AS production

# Copy built assets
COPY --from=builder /app/dist /usr/share/nginx/html

# Copy Nginx SPA configuration
COPY docker/nginx/spa.conf /etc/nginx/conf.d/default.conf

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]