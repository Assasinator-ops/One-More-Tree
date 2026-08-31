// Timeline.tsx - Clickable timeline component
import React from 'react';

interface TimelineStage {
  date: string;
  image: string;
  thumbnail: string;
  panoramic: boolean;
}

interface TimelineProps {
  stages: TimelineStage[];
  currentStage: number;
  onStageChange: (index: number) => void;
}

const Timeline: React.FC<TimelineProps> = ({ stages, currentStage, onStageChange }) => {
  return (
    <div className="space-y-4">
      <h3 className="text-lg font-semibold mb-4 tree-story-timeline-title">Growth Timeline</h3>
      <div className="space-y-3 max-h-[70vh] overflow-y-auto">
        {stages.map((stage, index) => (
          <div 
            key={index}
            className={`p-4 rounded-xl cursor-pointer transition-all group tree-story-timeline-item hover:bg-accent/50 border-2 ${
              index === currentStage 
                ? 'border-forest-500 bg-forest-50 ring-2 ring-forest-500/50' 
                : 'border-border hover:border-forest-400'
            }`}
            onClick={() => onStageChange(index)}
          >
            <div className="flex items-start gap-4">
              <div className="flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden ring-2 ring-background shadow-md">
                <img 
                  src={stage.thumbnail} 
                  alt={`Stage ${index + 1}`}
                  className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                />
              </div>
              <div className="flex-1 min-w-0">
                <div className="font-semibold text-foreground mb-1">{stage.date}</div>
                <div className="text-sm text-muted-foreground line-clamp-2">{stage.date} - Growth stage</div>
              </div>
            </div>
            {index === currentStage && (
              <div className="mt-3 pt-3 border-t border-border">
                <span className="text-xs text-forest-600 font-medium">Currently viewing</span>
              </div>
            )}
          </div>
        ))}
      </div>
    </div>
  );
};

export default Timeline;

