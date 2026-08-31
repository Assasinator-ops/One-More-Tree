import { Quote, Heart, Sparkles } from "lucide-react";

const TestimonialsSection = () => {
  const testimonials = [
    {
      quote:
        "The trees are growing and our community has a long-term project we can truly be proud of.",
      name: "Miriam, Community Lead",
      meta: "Savannah conservation",
      icon: Heart,
    },
    {
      quote:
        "Their monitoring helped us understand what worked, so we can improve survival rates every season.",
      name: "Dr. Chen, Research Partner",
      meta: "Forest restoration",
      icon: Sparkles,
    },
    {
      quote:
        "Transparent reporting and local ownership turned restoration into something that lasts.",
      name: "Ayo, Field Coordinator",
      meta: "Urban greening",
      icon: Quote,
    },
  ];

  return (
    <section id="testimonials" className="relative min-h-[65vh] w-full py-20 sm:py-32 overflow-hidden gradient-bg">
      <div className="absolute inset-0 leaf-pattern opacity-20" />
      <div className="absolute bottom-0 right-0 w-[620px] h-[620px] bg-forest-300/15 rounded-full blur-3xl" />

      <div className="relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20">
        <div className="max-w-7xl mx-auto">
          <div className="text-center mb-14 sm:mb-20">
            <div className="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-forest-100 border border-forest-200 mb-6">
              <Quote className="w-4 h-4 text-forest-600" />
              <span className="text-xs sm:text-sm text-forest-700 font-semibold uppercase tracking-wider">
                Testimonials
              </span>
            </div>
            <h2 className="font-serif text-3xl sm:text-4xl md:text-5xl font-bold text-foreground mb-4 leading-tight">
              Stories from the field
            </h2>
            <p className="text-base sm:text-lg text-foreground/60 max-w-2xl mx-auto">
              Restoration is more than planting. It is people, knowledge, and long-term care.
            </p>
          </div>

          <div className="grid md:grid-cols-3 gap-4 sm:gap-6">
            {testimonials.map((t, i) => (
              <div key={i} className="glass-card rounded-3xl p-6 sm:p-7 hover:bg-white transition-all shadow-soft">
                <div className="w-12 h-12 rounded-2xl bg-forest-100 flex items-center justify-center mb-4">
                  <t.icon className="w-5 h-5 text-forest-600" />
                </div>
                <div className="text-foreground/70 leading-relaxed mb-4">"{t.quote}"</div>
                <div className="font-serif text-lg font-bold text-foreground mb-1">{t.name}</div>
                <div className="text-sm text-foreground/50">{t.meta}</div>
              </div>
            ))}
          </div>

          <div className="mt-10 text-center">
            <div className="inline-flex items-center gap-3 px-5 py-4 rounded-3xl glass-card shadow-medium">
              <span className="text-sm text-foreground/60">Want to share your story?</span>
              <a
                href="#join"
                className="text-sm font-semibold text-forest-600 hover:text-forest-700 transition-colors"
              >
                Join the movement
              </a>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
};

export default TestimonialsSection;

