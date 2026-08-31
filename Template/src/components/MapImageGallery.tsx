import { useEffect, useState } from 'react';
import { ChevronLeft, ChevronRight, X } from 'lucide-react';

interface ProgressImage {
  title: string;
  description: string;
  image: string;
  date: string;
}

interface TreeEvent {
  id: string;
  title: string;
  description: string;
  eventType: 'planted' | 'protected' | 'irrigated' | 'growth_stage';
  stageKey?: 'seed' | 'growth' | 'mature';
  count: number;
  date: string;
  image?: string;
  verifiedBy?: string;
}

interface MapImageGalleryProps {
  name: string;
  region: string;
  description: string;
  treesPlanted: number;
  progressImages: ProgressImage[];
  events?: TreeEvent[];
  isOpen: boolean;
  onClose: () => void;
}

const MapImageGallery = ({
  name,
  region,
  description,
  treesPlanted,
  progressImages,
  events,
  isOpen,
  onClose
}: MapImageGalleryProps) => {
  const [currentImageIndex, setCurrentImageIndex] = useState(0);
  const [modalWidth, setModalWidth] = useState('1000px');

  useEffect(() => {
    const updateWidth = () => {
      const width = window.innerWidth;
      if (width < 640) setModalWidth('95vw');
      else if (width < 1024) setModalWidth('88vw');
      else setModalWidth('1000px');
    };

    updateWidth();
    window.addEventListener('resize', updateWidth);
    return () => window.removeEventListener('resize', updateWidth);
  }, []);

  useEffect(() => {
    // Reset slideshow when the modal opens or target changes.
    // Defer to next tick to satisfy eslint rule about state updates in effects.
    const id = window.setTimeout(() => {
      setCurrentImageIndex(0);
    }, 0);

    return () => window.clearTimeout(id);
  }, [name, isOpen]);

  if (!isOpen) return null;

  const currentImage = progressImages[currentImageIndex];

  const handleNext = () => {
    setCurrentImageIndex((prev) => (prev + 1) % progressImages.length);
  };

  const handlePrev = () => {
    setCurrentImageIndex((prev) => (prev - 1 + progressImages.length) % progressImages.length);
  };

  return (
    <div className="fixed inset-0 bg-black/30 backdrop-blur-sm z-50 flex items-center justify-center p-4" onClick={onClose}>
      <div
        className="bg-white border border-slate-200 rounded-3xl w-full max-h-[90vh] overflow-y-auto shadow-medium"
        style={{ maxWidth: modalWidth }}
        onClick={(e) => e.stopPropagation()}
      >
        <div className="sticky top-0 bg-white/95 p-6 border-b border-slate-200 flex items-start justify-between z-10">
          <div>
            <h2 className="font-serif text-2xl font-bold text-foreground mb-2">{name}</h2>
            <p className="text-sm text-forest-700">{region}</p>
          </div>
          <button
            onClick={onClose}
            className="p-2 hover:bg-slate-100 rounded-full transition-colors duration-300"
          >
            <X className="w-6 h-6 text-foreground" />
          </button>
        </div>

        <div className="p-6">
          <div className="relative mb-6">
            <img
              src={currentImage.image}
              alt={currentImage.title}
              className="w-full h-72 sm:h-[420px] object-cover rounded-2xl"
            />
            <div className="absolute inset-0 rounded-2xl bg-gradient-to-t from-slate-900/20 to-transparent flex items-end p-4">
              <p className="text-foreground/80 text-sm font-medium">{currentImage.date}</p>
            </div>
          </div>

          <div className="mb-6">
            <h3 className="font-serif text-xl font-semibold text-foreground mb-2">{currentImage.title}</h3>
            <p className="text-foreground/70 text-sm mb-4">{currentImage.description}</p>
          </div>

          <div className="flex items-center justify-between mb-6">
            <button
              onClick={handlePrev}
              className="p-2 hover:bg-forest-600/20 rounded-full transition-colors duration-300 text-forest-400 hover:text-forest-300"
            >
              <ChevronLeft className="w-6 h-6" />
            </button>

            <div className="flex gap-2">
              {progressImages.map((_, index) => (
                <button
                  key={index}
                  onClick={() => setCurrentImageIndex(index)}
                  className={`h-2 rounded-full transition-all duration-300 ${
                    index === currentImageIndex ? 'bg-forest-500 w-8' : 'bg-slate-700 w-2 hover:bg-slate-500'
                  }`}
                  aria-label={`Go to slide ${index + 1}`}
                />
              ))}
            </div>

            <button
              onClick={handleNext}
              className="p-2 hover:bg-forest-600/20 rounded-full transition-colors duration-300 text-forest-400 hover:text-forest-300"
            >
              <ChevronRight className="w-6 h-6" />
            </button>
          </div>

          <div className="text-center text-sm text-foreground/70 mb-6">
            {currentImageIndex + 1} of {progressImages.length} progress updates
          </div>

          <div className="bg-white/95 border border-slate-200 rounded-xl p-4 mb-6">
            <div className="grid grid-cols-2 gap-4">
              <div>
                <p className="text-xs text-foreground/60 mb-1">Project Location</p>
                <p className="text-foreground font-medium">{region}</p>
              </div>
              <div>
                <p className="text-xs text-foreground/60 mb-1">Trees Planted</p>
                <p className="text-foreground font-medium">{treesPlanted.toLocaleString()}</p>
              </div>
              <div className="col-span-2">
                <p className="text-xs text-foreground/60 mb-1">About This Project</p>
                <p className="text-foreground/70 text-sm">{description}</p>
              </div>
            </div>
          </div>

          {events && events.length > 0 && (
            <div className="bg-slate-50 border border-slate-200 rounded-3xl p-5 mb-6">
              <div className="flex items-center justify-between gap-3 mb-4">
                <div>
                  <h3 className="text-base font-semibold text-foreground">Recent milestones</h3>
                  <p className="text-sm text-foreground/60">Verified updates from the field.</p>
                </div>
              </div>
              <div className="space-y-4">
                {events.map((ev) => (
                  <div key={ev.id} className="rounded-2xl bg-white border border-slate-200 p-4">
                    <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                      <div>
                        <p className="text-sm font-semibold text-forest-700">{ev.title}</p>
                        <p className="text-xs text-foreground/60">{ev.date} • {ev.eventType.replace('_', ' ')}</p>
                      </div>
                      <div className="text-right">
                        <p className="text-sm font-semibold text-foreground">{ev.count.toLocaleString()}</p>
                        <p className="text-xs text-foreground/60">count</p>
                      </div>
                    </div>
                    <p className="mt-3 text-sm text-foreground/70">{ev.description}</p>
                    {ev.verifiedBy ? <p className="mt-3 text-xs text-foreground/60">Verified by {ev.verifiedBy}</p> : null}
                  </div>
                ))}
              </div>
            </div>
          )}

          <button className="w-full px-6 py-3 bg-forest-600 hover:bg-forest-700 text-white font-semibold rounded-full transition-all duration-300 shadow-medium hover:shadow-elevated">
            Support This Project
          </button>
        </div>
      </div>
    </div>
  );
};

export default MapImageGallery;
