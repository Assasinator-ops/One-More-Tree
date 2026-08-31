'use client';

import { useState } from 'react';
import { Mail, Phone, MapPin } from 'lucide-react';

export default function ContactView() {
  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [message, setMessage] = useState('');
  const [submitResult, setSubmitResult] = useState<string | null>(null);
  const [submitting, setSubmitting] = useState(false);

  const onSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setSubmitting(true);
    setSubmitResult(null);
    try {
      const res = await fetch('/api/public/contact', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ name, email, message })
      });
      const json = await res.json();
      if (!res.ok || !json.ok) {
        setSubmitResult(json.message ?? 'Unable to send message. Please try again later.');
      } else {
        setSubmitResult('Message sent! We saved it to the admin inbox.');
        setName('');
        setEmail('');
        setMessage('');
      }
    } catch {
      setSubmitResult('Unable to send message. Please try again later.');
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <div className="relative min-h-screen bg-background">
      <main className="relative">
        <section className="relative min-h-[55vh] flex items-center justify-center overflow-hidden page-header-texture">
          <div className="absolute inset-0 bg-gradient-to-b from-forest-50/95 to-background" />
          <div className="absolute inset-0 leaf-pattern opacity-20 pointer-events-none" />
          <div className="relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20 py-20 sm:py-32">
            <div className="max-w-5xl mx-auto text-center">
              <div className="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-forest-50 border border-slate-200 mb-6">
                <Mail className="w-4 h-4 text-forest-700" />
                <span className="text-xs sm:text-sm text-foreground font-semibold">Contact OneMoreTree</span>
              </div>
              <h1 className="font-serif text-4xl sm:text-5xl md:text-6xl font-bold text-foreground mb-4 leading-tight">
                Let us connect
              </h1>
              <p className="text-lg sm:text-xl text-foreground/70 max-w-3xl mx-auto">
                Send a message and we will get back to you with guidance on volunteering, partnerships, or donations.
              </p>
            </div>
          </div>
        </section>

        <section className="py-20 sm:py-32 px-4 sm:px-6 lg:px-12 xl:px-20">
          <div className="max-w-6xl mx-auto">
            <div className="grid lg:grid-cols-2 gap-8 sm:gap-10 items-start">
              <div className="bg-white border border-earth-200 rounded-3xl p-6 sm:p-8 shadow-medium">
                <div className="font-serif text-2xl font-bold text-foreground mb-2">Send a message</div>
                <div className="text-sm text-foreground/70 mb-6">
                  We respond as soon as possible. Thanks for supporting forest restoration.
                </div>

                <form onSubmit={onSubmit} className="space-y-4">
                  <div>
                    <label className="text-sm text-foreground/70">Name</label>
                    <input
                      value={name}
                      onChange={(e) => setName(e.target.value)}
                      className="mt-1 w-full px-4 py-3 rounded-xl bg-earth-50 text-foreground placeholder-foreground/40 border border-earth-200 focus:border-forest-500 focus:outline-none transition-colors"
                      placeholder="Your name"
                    />
                  </div>

                  <div>
                    <label className="text-sm text-foreground/70">Email</label>
                    <input
                      value={email}
                      onChange={(e) => setEmail(e.target.value)}
                      type="email"
                      className="mt-1 w-full px-4 py-3 rounded-xl bg-earth-50 text-foreground placeholder-foreground/40 border border-earth-200 focus:border-forest-500 focus:outline-none transition-colors"
                      placeholder="you@example.com"
                    />
                  </div>

                  <div>
                    <label className="text-sm text-foreground/70">Message</label>
                    <textarea
                      value={message}
                      onChange={(e) => setMessage(e.target.value)}
                      rows={5}
                      className="mt-1 w-full px-4 py-3 rounded-xl bg-earth-50 text-foreground placeholder-foreground/40 border border-earth-200 focus:border-forest-500 focus:outline-none transition-colors"
                      placeholder="Tell us how we can help…"
                    />
                  </div>

                  <button
                    type="submit"
                    disabled={submitting}
                    className="w-full px-8 py-3 bg-forest-600 hover:bg-forest-700 text-white font-semibold rounded-xl transition-all duration-300 hover:shadow-elevated disabled:opacity-70"
                  >
                    {submitting ? 'Sending…' : 'Send message'}
                  </button>
                </form>

                {submitResult ? (
                  <div className="mt-4 rounded-2xl border border-earth-200 bg-earth-50 p-4 text-sm text-foreground/70">
                    {submitResult}
                  </div>
                ) : null}
              </div>

              <div className="space-y-4">
                {[
                  { icon: MapPin, title: 'Address', desc: '123 Green Street, Portland, OR 97201' },
                  { icon: Mail, title: 'Email', desc: 'hello@onemoretree.org' },
                  { icon: Phone, title: 'Phone', desc: '(503) 555-1234' }
                ].map((item, i) => (
                  <div key={i} className="bg-white border border-earth-200 rounded-3xl p-6 sm:p-7 shadow-medium">
                    <div className="flex items-center gap-3 mb-2">
                      <item.icon className="w-5 h-5 text-forest-600" />
                      <div className="font-serif text-xl font-bold text-foreground">{item.title}</div>
                    </div>
                    <div className="text-sm text-foreground/70 leading-relaxed">{item.desc}</div>
                  </div>
                ))}
              </div>
            </div>
          </div>
        </section>
      </main>
    </div>
  );
}
