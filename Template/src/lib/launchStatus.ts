export const LAUNCH_DATE = new Date('2026-12-01');

export const isLaunched = () => {
  return new Date() >= LAUNCH_DATE;
};

export const getStatusMessage = () => {
  if (isLaunched()) {
    return '';
  }
  return 'Launching December 2026 – Join our founding team now!';
};

export const getCtaText = (isLaunched: boolean) => {
  return isLaunched ? 'Plant Your First Tree' : 'Be a Founding Partner';
};

