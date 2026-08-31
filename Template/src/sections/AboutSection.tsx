import { Heart, Leaf, Shield, Sprout, Globe } from "lucide-react";

const AboutSection = () => {
  const values = [
    {
      icon: Sprout,
      title: "Start with seeds",
      desc: "We plant native trees using science-backed methods for long-term growth.",
    },
    {
      icon: Leaf,
      title: "Respect ecosystems",
      desc: "Restoration must protect biodiversity and strengthen local environments.",
    },
    {
      icon: Shield,
      title: "Commit for the long run",
      desc: "We focus on survival rates, maintenance, and measurable outcomes.",
    },
    {
      icon: Heart,
      title: "Work with communities",
      desc: "Local partners lead, we support with resources, training, and transparency.",
    },
  ];

  return (
    <section id="about" className="relative min-h-[70vh] w-full py-20 sm:py-32 overflow-hidden gradient-bg">
      <div className="absolute inset-0 leaf-pattern opacity-20" />
      <div className="absolute top-0 right-0 w-[520px] h-[520px] bg-forest-300/15 rounded-full blur-3xl" />

      <div className="relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20">
        <div className="max-w-7xl mx-auto">
          <div className="text-center mb-14 sm:mb-20">
            <div className="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-forest-100 border border-forest-200 mb-6">
              <Globe className="w-4 h-4 text-forest-600" />
              <span className="text-xs sm:text-sm text-forest-700 font-semibold uppercase tracking-wider">
                Our Story
              </span>
            </div>
            <h2 className="font-serif text-3xl sm:text-4xl md:text-5xl font-bold text-foreground mb-4 sm:mb-6 leading-tight">
              Growing forests, building hope in Spain
            </h2>
            <p className="text-base sm:text-lg text-foreground/60 max-w-2xl mx-auto">
              From a conversation among friends to a movement restoring forests across Spain. We plant trees in schools, sports complexes, senior centers, wildfire-damaged forests, and along the Camino de Santiago.
            </p>
          </div>

          <div className="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            {values.map((value, i) => (
              <div key={i} className="glass-card rounded-2xl p-6 sm:p-7">
                <div className="w-12 h-12 rounded-xl bg-forest-100 flex items-center justify-center mb-4">
                  <value.icon className="w-5 h-5 text-forest-600" />
                </div>
                <div className="font-serif text-xl font-bold text-foreground mb-2">{value.title}</div>
                <div className="text-sm text-foreground/60 leading-relaxed">{value.desc}</div>
              </div>
            ))}
          </div>

          <div className="mt-10 glass-card rounded-3xl p-6 sm:p-10 flex flex-col lg:flex-row gap-8 items-start lg:items-center shadow-medium">
            <div className="flex-1">
              <div className="text-sm text-foreground/50 mb-2">What we deliver</div>
              <div className="font-serif text-2xl sm:text-3xl font-bold text-foreground mb-4">
                Measurable restoration you can trust
              </div>
              <p className="text-sm sm:text-base text-foreground/60 max-w-2xl">
                From native species selection to long-term monitoring, our programs are designed to
                maximize survival and restore ecosystems responsibly.
              </p>
            </div>
            <div className="flex gap-4">
              {[
                { k: "3M", t: "Trees by 2040" },
                { k: "Native", t: "Species Focus" },
                { k: "Local", t: "Community-led" },
              ].map((stat, idx) => (
                <div key={idx} className="text-center glass-card rounded-2xl p-4 w-[140px] shadow-soft">
                  <div className="font-serif text-3xl font-bold text-forest-600 mb-1">{stat.k}</div>
                  <div className="text-xs text-foreground/60">{stat.t}</div>
                </div>
              ))}
            </div>
          </div>
        </div>
      </div>
    </section>
  );
};

export default AboutSection;

