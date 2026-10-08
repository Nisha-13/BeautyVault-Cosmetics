# Build stage
FROM node:20-alpine AS builder

ARG VITE_API_URL=http://localhost/api/v1
ARG VITE_REVERB_APP_KEY
ARG VITE_REVERB_HOST=localhost
ARG VITE_REVERB_PORT=8080
ARG VITE_REVERB_SCHEME=http

ENV VITE_API_URL=${VITE_API_URL}
ENV VITE_REVERB_APP_KEY=${VITE_REVERB_APP_KEY}
ENV VITE_REVERB_HOST=${VITE_REVERB_HOST}
ENV VITE_REVERB_PORT=${VITE_REVERB_PORT}
ENV VITE_REVERB_SCHEME=${VITE_REVERB_SCHEME}

WORKDIR /app

# Copy frontend package files
COPY frontend/package*.json ./
RUN npm ci --prefer-offline

# Copy frontend source
COPY frontend/ ./

# Build frontend
RUN npm run build


# Production stage - serve with Nginx
FROM nginx:alpine

# Copy built assets
COPY --from=builder /app/dist /usr/share/nginx/html

# Copy Nginx SPA configuration
COPY docker/nginx/spa.conf /etc/nginx/conf.d/default.conf

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
