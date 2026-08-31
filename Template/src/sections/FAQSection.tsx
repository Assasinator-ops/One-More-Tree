'use client';

import { useEffect, useState } from 'react';
import { Accordion, AccordionContent, AccordionItem, AccordionTrigger } from '@/components/ui/accordion';
import { Leaf } from 'lucide-react';
import { publicApi, PublicKeys } from '@/lib/publicApi';

type AdminFaqItem = { id: string; question: string; answer: string };

function getFallbackFaqs(): Array<{ q: string; a: string }> {
  return [
    {
      q: 'Do you plant native species only?',
      a: 'Yes. We focus on native species suited to the local ecosystem to support biodiversity and long-term survival.',
    },
    {
      q: 'How do you measure impact?',
      a: 'We track survival rates and development over time, and we publish updates so partners and supporters can see progress.',
    },
    {
      q: 'Can communities participate?',
      a: 'Absolutely. We work with local leaders and training partners to support planting, maintenance, and stewardship.',
    },
    {
      q: 'How can I get involved?',
      a: 'Donate, volunteer, or partner with us. Use the “Join the Movement” section to take your next step.',
    },
  ];
}



const FAQSection = () => {
  const [adminFaqs, setAdminFaqs] = useState<AdminFaqItem[] | null>(null);

  useEffect(() => {
    let mounted = true;
    (async () => {
      try {
        const data = await publicApi.read<{ items?: AdminFaqItem[] }>(PublicKeys.FAQ);
        const items = (data as any)?.items;
        if (!mounted) return;
        setAdminFaqs(Array.isArray(items) ? items : null);
      } catch {
        if (!mounted) return;
        setAdminFaqs(null);
      }
    })();

    return () => {
      mounted = false;
    };
  }, []);


  const faqs = adminFaqs?.length
    ? adminFaqs.map((item) => ({ q: item.question, a: item.answer }))
    : [];

  return (
    <section id="faq" className="relative min-h-[60vh] w-full py-20 sm:py-32 overflow-hidden gradient-bg">
      <div className="absolute inset-0 leaf-pattern opacity-20" />
      <div className="absolute top-0 left-0 w-[520px] h-[520px] bg-forest-300/15 rounded-full blur-3xl" />

      <div className="relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20">
        <div className="max-w-7xl mx-auto">
          <div className="text-center mb-14 sm:mb-20">
            <div className="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-forest-100 border border-forest-200 mb-6">
              <Leaf className="w-4 h-4 text-forest-600" />
              <span className="text-xs sm:text-sm text-forest-700 font-semibold uppercase tracking-wider">
                FAQ
              </span>
            </div>
            <h2 className="font-serif text-3xl sm:text-4xl md:text-5xl font-bold text-foreground mb-4 leading-tight">
              Questions, answered
            </h2>
            <p className="text-base sm:text-lg text-foreground/60 max-w-2xl mx-auto">
              Clear answers about how we plant, measure impact, and partner with communities.
            </p>
          </div>

          <div className="grid lg:grid-cols-2 gap-6 sm:gap-8 items-start">
            <div className="glass-card rounded-3xl p-6 sm:p-8 shadow-medium">
              <Accordion type="single" collapsible className="w-full">
                {faqs.map((f, i) => (
                  <AccordionItem key={i} value={`item-${i + 1}`}>
                    <AccordionTrigger className="text-foreground hover:text-forest-600 hover:underline">
                      {f.q}
                    </AccordionTrigger>
                    <AccordionContent className="text-foreground/70">{f.a}</AccordionContent>
                  </AccordionItem>
                ))}
              </Accordion>
            </div>

            <div className="glass-card rounded-3xl p-6 sm:p-8 shadow-medium">
              <div className="font-serif text-2xl font-bold text-foreground mb-2">Still have questions?</div>
              <p className="text-sm text-foreground/60 mb-6">
                Reach out and we will help you find the best way to support restoration.
              </p>
              <a
                href="#join"
                className="inline-flex items-center justify-center w-full px-6 py-3 bg-forest-600 hover:bg-forest-700 text-white font-semibold rounded-xl transition-all duration-300 shadow-medium hover:shadow-elevated"
              >
                Contact us via Join Us
              </a>
              <div className="mt-5 text-xs text-foreground/40">
                Tip: donate, volunteer, or partner to make an immediate impact.
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
};

export default FAQSection;

