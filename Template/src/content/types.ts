import { z } from 'zod';

/**
 * Contract-first content model.
 *
 * The public UI should only consume data that validates against these schemas.
 */

export const HeroContentSchema = z.object({
  missionBadge: z.string().min(1),
  headlineTop: z.string().min(1),
  headlineEmphasis: z.string().min(1),
  subtitle: z.string().min(1),
  primaryCtaText: z.string().min(1),
  secondaryCtaText: z.string().min(1),
  stats: z.array(
    z.object({
      value: z.string().min(1),
      label: z.string().min(1),
    }),
  ),
});

export type HeroContent = z.infer<typeof HeroContentSchema>;

export const ImpactContentSchema = z.object({
  sectionTitle: z.string().min(1),
  headline: z.string().min(1),
  description: z.string().min(1),
  treesPlantedLabel: z.string().min(1),
  goalBy2040Label: z.string().min(1),
  goalBy2040Value: z.string().min(1),
  progressText: z.string().min(1),
  toGoText: z.string().min(1),
  stats: z.array(
    z.object({
      value: z.string().min(1),
      label: z.string().min(1),
      desc: z.string().min(1),
    }),
  ),
});

export type ImpactContent = z.infer<typeof ImpactContentSchema>;

export const TeamMemberSchema = z.object({
  name: z.string().min(1),
  role: z.string().min(1),
  image: z.string().optional(),
  highlight: z.string().optional(),
});

export type TeamMember = z.infer<typeof TeamMemberSchema>;

export const TeamStatSchema = z.object({
  value: z.string().min(1),
  label: z.string().min(1),
});

export type TeamStat = z.infer<typeof TeamStatSchema>;

export const TeamContentSchema = z.object({
  sectionTitle: z.string().min(1),
  headline: z.string().min(1),
  description: z.string().min(1),
  members: z.array(TeamMemberSchema).min(0),
  stats: z.array(TeamStatSchema).min(0),
});

export type TeamContent = z.infer<typeof TeamContentSchema>;

export const StoryFeatureSchema = z.object({
  icon: z.string().min(1),
  label: z.string().min(1),
  desc: z.string().min(1),
});

export type StoryFeature = z.infer<typeof StoryFeatureSchema>;

export const StoryHighlightSchema = z.object({
  value: z.string().min(1),
  title: z.string().min(1),
  subtitle: z.string().min(1),
});

export type StoryHighlight = z.infer<typeof StoryHighlightSchema>;

export const StoryChapterSchema = z.object({
  key: z.enum(['seed', 'growth', 'mature']),
  chapterLabel: z.string().min(1),
  headline: z.string().min(1),
  headlineEmphasis: z.string().optional(),
  descriptions: z.tuple([z.string().min(1), z.string().min(1)]),
  imageSrc: z.string().min(1),
  imageAlt: z.string().min(1),
  infoCards: z.array(StoryFeatureSchema).min(1),
  highlight: StoryHighlightSchema.optional(),
});

export type StoryChapter = z.infer<typeof StoryChapterSchema>;

export const StoriesContentSchema = z.object({
  chapters: z.array(StoryChapterSchema).min(0).max(3),
});

export type StoriesContent = z.infer<typeof StoriesContentSchema>;

export const ProgressImageSchema = z.object({
  title: z.string().min(1),
  description: z.string().min(1),
  image: z.string().min(1),
  date: z.string().min(1),
});

export type ProgressImage = z.infer<typeof ProgressImageSchema>;

export const TreeEventSchema = z.object({
  id: z.string().min(1),
  title: z.string().min(1),
  description: z.string().min(1),
  eventType: z.enum(['planted', 'protected', 'irrigated', 'growth_stage']),
  stageKey: z.enum(['seed', 'growth', 'mature']).optional(),
  count: z.number().min(0),
  date: z.string().min(1),
  image: z.string().optional(),
  verifiedBy: z.string().optional(),
});

export type TreeEvent = z.infer<typeof TreeEventSchema>;

export const PlantingLocationSchema = z.object({
  id: z.string().min(1),
  name: z.string().min(1),
  region: z.string().min(1),
  description: z.string().min(1),
  coords: z.object({ x: z.string().min(1), y: z.string().min(1) }),
  treesPlanted: z.number(),
  volunteers: z.number(),
  progressImages: z.array(ProgressImageSchema).min(1),
  events: z.array(TreeEventSchema).optional(),
});

export type PlantingLocation = z.infer<typeof PlantingLocationSchema>;

export const LiveMapContentSchema = z.object({
  sectionTitle: z.string().min(1),
  headline: z.string().min(1),
  description: z.string().min(1),
  locations: z.array(PlantingLocationSchema).min(0),
});

export type LiveMapContent = z.infer<typeof LiveMapContentSchema>;

export const FooterContentSchema = z.object({
  mission: z.string().min(1),
});

export type FooterContent = z.infer<typeof FooterContentSchema>;

export const ModuleStatusSchema = z.object({
  // Stable key used by front-end to decide whether a module/section should render.
  key: z.string().min(1),
  enabled: z.boolean(),
  updatedAt: z.string().min(1).optional(),
});

export const PageContentSchema = z.object({
  hero: HeroContentSchema,
  impact: ImpactContentSchema,
  team: TeamContentSchema,
  stories: StoriesContentSchema,
  liveMap: LiveMapContentSchema,
  footer: FooterContentSchema,
  // MVP toggle support (single source of truth for visibility)
  modules: z.array(ModuleStatusSchema).default([]),
});

export type PageContent = z.infer<typeof PageContentSchema>;

export type ContentKey = keyof PageContent;

export type ModuleStatus = PageContent['modules'][number];

