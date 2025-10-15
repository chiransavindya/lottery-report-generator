# docker/node/Dockerfile
FROM node:18 as builder
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build   # assumes build outputs to 'dist' or 'build' - check your repo (likely vite -> dist)

# stage to copy outputs (no runtime)
FROM alpine:3.18
WORKDIR /app
COPY --from=builder /app/dist ./dist   # adjust if vite outputs 'dist' or 'build'