/** @type {import('next').NextConfig} */
const nextConfig = {
  reactStrictMode: true,
  output: 'export',
  experimental: {
    optimizePackageImports: [
      "lucide-react",
      "sonner"
    ]
  }
};

export default nextConfig;

