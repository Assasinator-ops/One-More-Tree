// PanoramaViewer.tsx - Image viewer with panorama support
import React, { useEffect, useRef, useState } from 'react';

interface PanoramaViewerProps {
  image: string;
  panoramic: boolean;
}

const PanoramaViewer: React.FC<PanoramaViewerProps> = ({ image, panoramic }) => {
  const containerRef = useRef<HTMLDivElement>(null);
  const [isLoaded, setIsLoaded] = useState(false);

  useEffect(() => {
    // Reset on new image
    // React prefers state changes in the same render cycle to avoid cascading updates.

    // Using a microtask ensures this reset doesn't happen during the effect flush.
    queueMicrotask(() => setIsLoaded(false));
  }, [image]);

  if (panoramic) {
    // Placeholder for panorama viewer (photo-sphere-viewer would go here)
    // Fallback to zoomable image for now
    return (
      <div ref={containerRef} className="w-full h-[400px] md:h-[500px] rounded-2xl overflow-hidden tree-story-panorama bg-gradient-to-br from-forest-50 to-earth-50 flex items-center justify-center shadow-xl">
        <div className="text-center p-8">
          <div className="w-24 h-24 rounded-full bg-forest-100 flex items-center justify-center mx-auto mb-4">
            <span className="text-2xl">360°</span>
          </div>
          <p className="text-foreground/60">Panoramic view (coming soon)</p>
          <img 
            src={image} 
            alt="Panorama fallback"
            className="mt-4 max-w-full h-auto rounded-xl shadow-lg"
          />
        </div>
      </div>
    );
  }

  return (
    <div className="w-full h-[400px] md:h-[500px] rounded-2xl overflow-hidden tree-story-image-viewer bg-gradient-to-br from-forest-50 to-earth-50 shadow-xl relative">
      <img 
        src={image} 
        alt="Tree growth stage"
        className="w-full h-full object-contain p-8 transition-all duration-500 hover:scale-105 cursor-zoom-in"
        onLoad={() => setIsLoaded(true)}
        style={{ opacity: isLoaded ? 1 : 0 }}
      />
      {!isLoaded && (
        <div className="absolute inset-0 flex items-center justify-center">
          <div className="w-12 h-12 border-4 border-forest-200 border-t-forest-500 rounded-full animate-spin" />
        </div>
      )}
    </div>
  );
};

export default PanoramaViewer;

