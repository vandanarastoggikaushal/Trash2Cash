/** @type {import('next').NextConfig} */
const nextConfig = {
  reactStrictMode: true,
  output: 'standalone',
  experimental: {
    optimizePackageImports: [
      "lucide-react",
      "sonner"
    ]
  }
};

export default nextConfig;

