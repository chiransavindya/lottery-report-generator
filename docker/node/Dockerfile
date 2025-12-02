# ---------- Build Stage ----------
FROM node:18-alpine AS builder

WORKDIR /app

# Cache dependencies
COPY package*.json ./
RUN --mount=type=cache,target=/root/.npm npm ci

COPY . .
RUN npm run build

# ---------- Production Stage ----------
FROM nginx:1.24-alpine

# Copy React build
COPY --from=builder /app/build /var/www/public/build

# Copy Laravel Nginx config (updated production)
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# Expose port
EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
