import sharp from 'sharp';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));

async function generateFavicon() {
  try {
    // Generate favicon.ico (32x32)
    await sharp(path.join(__dirname, 'public/logo-colorful.png'))
      .resize(32, 32, {
        fit: 'contain',
        background: { r: 255, g: 255, b: 255, alpha: 1 }
      })
      .toFile(path.join(__dirname, 'public/favicon.ico'));

    // Generate apple touch icon (180x180)
    await sharp(path.join(__dirname, 'public/logo-colorful.png'))
      .resize(180, 180, {
        fit: 'contain',
        background: { r: 255, g: 255, b: 255, alpha: 1 }
      })
      .toFile(path.join(__dirname, 'public/apple-touch-icon.png'));

    // Generate favicon.png (192x192) for Android
    await sharp(path.join(__dirname, 'public/logo-colorful.png'))
      .resize(192, 192, {
        fit: 'contain',
        background: { r: 255, g: 255, b: 255, alpha: 1 }
      })
      .toFile(path.join(__dirname, 'public/favicon-192.png'));

    console.log('✅ Favicons generated successfully!');
  } catch (error) {
    console.error('❌ Error generating favicons:', error.message);
    process.exit(1);
  }
}

generateFavicon();
