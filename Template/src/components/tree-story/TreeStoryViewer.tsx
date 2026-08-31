// TreeStoryViewer.tsx - Modular storytelling overlay (per map.md)
import React, { useState, useCallback } from 'react';
import { Sheet, SheetContent, SheetClose } from '@/components/ui/sheet';
import Timeline from './Timeline';
import PanoramaViewer from './PanoramaViewer';
import { X } from 'lucide-react';

interface TreeStoryData {
  id: string;
  stages: Array<{
    date: string;
    image: string;
    thumbnail: string;
    panoramic: boolean;
  }>;
}

interface TreeStoryViewerProps {
  targetId: string;
  data: TreeStoryData;
  onClose: () => void;
}

const TreeStoryViewer: React.FC<TreeStoryViewerProps> = ({ targetId, data, onClose }) => {
  const [currentStage, setCurrentStage] = useState(0);

  const handleImageChange = useCallback((index: number) => {
    setCurrentStage(index);
    // Emit custom event
    window.dispatchEvent(new CustomEvent('story:imageChange', { detail: { index } }));
  }, []);

  return (
    <Sheet open={true} onOpenChange={onClose}>
      <SheetContent side="right" className="w-[90vw] sm:w-[80vw] md:w-[60vw] lg:w-[50vw] max-w-4xl p-0 tree-story-panel">
        <div className="h-full flex flex-col">
          <div className="p-6 border-b border-border flex items-center justify-between">
            <h2 className="text-2xl font-bold">Tree Growth Story - Target {targetId}</h2>
            <SheetClose asChild>
              <button className="p-2 hover:bg-accent rounded-lg transition-colors">
                <X className="w-5 h-5" />
              </button>
            </SheetClose>
          </div>
          <div className="flex-1 grid grid-cols-1 lg:grid-cols-2 h-0 overflow-hidden">
            <div className="p-6 border-r border-border tree-story-viewer">
              <PanoramaViewer 
                image={data.stages[currentStage].image} 
                panoramic={data.stages[currentStage].panoramic}
              />
            </div>
            <div className="p-6 tree-story-timeline">
              <Timeline stages={data.stages} currentStage={currentStage} onStageChange={handleImageChange} />
            </div>
          </div>
        </div>
      </SheetContent>
    </Sheet>
  );
};

export default TreeStoryViewer;

