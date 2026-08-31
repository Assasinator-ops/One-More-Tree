import { useState } from 'react';
import MapImageGallery from './MapImageGallery';
import { europePlantingLocations, type PlantingLocation } from '@/lib/europePlantingLocations';
import { MapPin } from 'lucide-react';

type InteractiveSpainMapProps = {
  locations?: PlantingLocation[];
};

const InteractiveSpainMap = ({ locations = europePlantingLocations }: InteractiveSpainMapProps) => {
  const [selectedLocation, setSelectedLocation] = useState<PlantingLocation | null>(null);
  const [isGalleryOpen, setIsGalleryOpen] = useState(false);

  const handleLocationClick = (location: typeof europePlantingLocations[0]) => {
    setSelectedLocation(location);
    setIsGalleryOpen(true);
  };

  return (
    <>
      <div className="relative w-full bg-gradient-to-br from-forest-50/90 to-background rounded-2xl overflow-hidden border border-slate-200/80 shadow-soft">
        {/* Europe Map Background */}
        <div
          className="relative w-full h-96 sm:h-[520px] bg-cover bg-center"
          style={{
            backgroundImage:
              'linear-gradient(135deg, rgba(237, 249, 237, 0.85) 0%, rgba(229, 245, 232, 0.8) 100%), url("data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22%3E%3Crect fill=%22%23f0f9ff%22 width=%22100%22 height=%22100%22/%3E%3Cpath fill=%22%231e5631%22 d=%22M25 20 L30 25 L28 35 L22 32 Z%22/%3E%3Cpath fill=%22%23e8f5e9%22 d=%22M35 30 L45 25 L50 40 L40 42 Z%22/%3E%3Cpath fill=%22%231e5631%22 d=%22M55 15 L70 20 L65 40 L50 35 Z%22/%3E%3C/svg%3E")',
            backgroundSize: 'cover'
          }}
        >
          <svg
            className="absolute inset-0 w-full h-full"
            viewBox="0 0 100 100"
            preserveAspectRatio="xMidYMid slice"
          >
            <path
              d="M 20 25 L 35 20 L 40 18 L 45 20 L 50 18 L 55 22 L 60 20 L 65 25 L 68 30 L 70 35 L 68 40 L 70 45 L 65 48 L 60 50 L 55 52 L 50 55 L 45 54 L 40 56 L 35 58 L 30 60 L 25 58 L 22 55 L 20 50 L 18 45 L 20 40 L 18 35 L 20 30 Z"
              fill="none"
              stroke="rgba(255, 255, 255, 0.2)"
              strokeWidth="0.5"
            />
          </svg>

          <div className="absolute inset-0 w-full h-full">
            {locations.map((location) => (
              <button
                key={location.id}
                onClick={() => handleLocationClick(location)}
                className="absolute transform -translate-x-1/2 -translate-y-1/2 group transition-all duration-300 hover:scale-110"
                style={{ left: location.coords.x, top: location.coords.y }}
                title={location.name}
              >
                <div
                  className="absolute inset-0 w-8 h-8 -translate-x-1/2 -translate-y-1/2 rounded-full border-2 border-forest-400 opacity-20 group-hover:opacity-40 transition-all duration-300 animate-pulse"
                />
                <div className="relative w-8 h-8 -translate-x-1/2 -translate-y-1/2 flex items-center justify-center">
                  <div className="absolute inset-0 bg-gradient-to-br from-forest-400 to-forest-600 rounded-full shadow-lg group-hover:shadow-2xl transition-all duration-300 group-hover:from-forest-300 group-hover:to-forest-500" />
                  <MapPin className="w-4 h-4 text-white relative z-10" />
                </div>
                <div className="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none z-20">
                  <div className="bg-white/95 border border-slate-200 rounded-lg p-2 sm:p-3 whitespace-nowrap text-xs sm:text-sm text-foreground shadow-soft">
                    <p className="font-semibold text-forest-700">{location.name}</p>
                    <p className="text-foreground/70">{location.treesPlanted.toLocaleString()} trees</p>
                  </div>
                  <div className="absolute top-full left-1/2 -translate-x-1/2 translate-y-px border-8 border-transparent border-t-white/95" />
                </div>
              </button>
            ))}
          </div>

          <div className="absolute bottom-4 left-4 right-4 bg-white/90 backdrop-blur-sm border border-slate-200 rounded-lg p-3 sm:p-4">
            <p className="text-xs sm:text-sm text-foreground/85">
              <span className="text-forest-700 font-semibold">💡 Tip:</span> Click on any location marker to see planting progress and image galleries
            </p>
          </div>
        </div>
      </div>

      <div className="mt-8 grid sm:grid-cols-2 gap-4 sm:gap-6">
        {locations.map((location) => (
          <button
            key={location.id}
            onClick={() => handleLocationClick(location)}
            className="bg-white/90 border border-slate-200 rounded-xl p-4 text-left hover:bg-white hover:border-forest-300/30 transition-all duration-300 group"
          >
            <div className="flex items-start justify-between mb-2">
              <div>
                <h3 className="font-semibold text-foreground group-hover:text-forest-700 transition-colors duration-300">
                  {location.name}
                </h3>
                <p className="text-xs text-forest-600">{location.region}</p>
              </div>
              <div className="text-right">
                <p className="text-sm font-semibold text-forest-700">{location.treesPlanted.toLocaleString()}</p>
                <p className="text-xs text-foreground/60">trees</p>
              </div>
            </div>
            <p className="text-sm text-foreground/75">{location.description}</p>
            <div className="mt-3 inline-block px-3 py-1 bg-forest-100 rounded-full text-xs text-forest-700 group-hover:bg-forest-200 transition-all duration-300">
              View Progress →
            </div>
          </button>
        ))}
      </div>

      {selectedLocation && (
        <MapImageGallery
          name={selectedLocation.name}
          region={selectedLocation.region}
          description={selectedLocation.description}
          treesPlanted={selectedLocation.treesPlanted}
          progressImages={selectedLocation.progressImages}
          events={selectedLocation.events}
          isOpen={isGalleryOpen}
          onClose={() => setIsGalleryOpen(false)}
        />
      )}
    </>
  );
};

export default InteractiveSpainMap;

