import path from 'node:path';
import { fileURLToPath } from 'node:url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));

/** @type {import('next').NextConfig} */
const nextConfig = {
  reactStrictMode: true,
  // Use a stable path alias that matches tsconfig "@/*"
  turbopack: {
    resolveAlias: {
      '@': path.resolve(__dirname, 'src')
    }
  },
  // Mark `node:sqlite` as external so Next doesn't try to bundle it
  // (it's a built-in Node module).
  serverExternalPackages: ['node:sqlite']
};

export default nextConfig;
