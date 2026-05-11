import { writeFile } from 'node:fs/promises';
import { imageToWebp } from 'imgtowebp/node';

const [, , inputPath, outputPath, rawOptions = '{}'] = process.argv;

if (!inputPath || !outputPath) {
  console.error('Usage: node scripts/convert-image-to-webp.mjs <input> <output> [json-options]');
  process.exit(2);
}

let options;
try {
  options = JSON.parse(rawOptions);
} catch (error) {
  console.error(`Invalid JSON options: ${error.message}`);
  process.exit(2);
}

try {
  const result = await imageToWebp(inputPath, {
    ...options,
    force: true,
    returnFile: false,
  });

  const bytes = Buffer.from(await result.blob.arrayBuffer());
  await writeFile(outputPath, bytes);

  console.log(JSON.stringify({
    isWebp: result.isWebp,
    width: result.width,
    height: result.height,
    quality: result.quality,
    bytes: bytes.byteLength,
  }));
} catch (error) {
  console.error(error instanceof Error ? error.message : String(error));
  process.exit(1);
}
