export const letter = {
  lead:
    "We founded North Shore Red because we care deeply about the future of the North Shore, Milwaukee, Wisconsin, and our country — and because the grassroots work being done here deserved an organization that could raise and allocate its own funds.",
  body: [
    "The volunteers behind this organization have run a campaign office, distributed yard signs across Milwaukee County, hosted major fundraising events, and built a county-leading canvassing effort. North Shore Red gives that work the independence and focus to keep growing.",
    "Our aim is plain. Defend the values that made America strong — individual liberty, limited government, fiscal responsibility, free markets, and a strong national defense — and help elect principled conservatives who will stand for them. We invite every conservative on the North Shore to join us in this chapter."
  ],
  signoff: "— The founding board of North Shore Red"
};

export const volunteerAreas = [
  {
    title: "Canvassing",
    body: "Voter conversations on approved routes, with training for anyone who has never done it."
  },
  {
    title: "Voter outreach",
    body: "Postcards, phone calls and neighbor-to-neighbor follow-up you can do from home."
  },
  {
    title: "Events",
    body: "Setup, check-in, hospitality and logistics that make a gathering welcoming."
  },
  {
    title: "Operations",
    body: "Scheduling, supplies, communications and coordination behind the scenes."
  }
];

export const nextEvent = {
  statusLabel: "Next gathering",
  name: "Conservative Cocktails",
  description:
    "Planning is underway for our next Conservative Cocktails gathering. The date, venue, keynote speaker, and attendance fee will be announced to invited guests.",
  details: [
    { label: "Date & venue", value: "TBD" },
    { label: "Keynote", value: "TBD" },
    { label: "Attendance fee", value: "TBD" }
  ],
  hostNote: "Requesting an invitation does not reserve a ticket or require payment.",
  footNote:
    "Add your name to the invitation list now and a board member will follow up when the program and attendance details are ready."
};

export const previousEvent = {
  name: "The North Shore Red Garden Party",
  description:
    "A look back at North Shore Red's September 2026 gathering, featuring former Governor Tommy Thompson and an evening of conversation among attendees from across the North Shore.",
  photos: [
    {
      src: "/assets/events/2026-garden-party/garden-party-program.jpg",
      alt: "Attendees listening as former Governor Tommy Thompson speaks at the 2026 North Shore Red Garden Party",
      caption: "Attendees listen to Tommy Thompson"
    },
    {
      src: "/assets/events/2026-garden-party/garden-party-table.jpg",
      alt: "Attendees discussing upcoming elections around a dining table at the 2026 North Shore Red Garden Party",
      caption: "Attendees discuss upcoming elections"
    },
    {
      src: "/assets/events/2026-garden-party/garden-party-refreshments.jpg",
      alt: "Refreshments prepared for guests at the 2026 North Shore Red Garden Party",
      caption: "Prepared for guests"
    },
    {
      src: "/assets/events/2026-garden-party/garden-party-moonrise.jpg",
      alt: "Moonrise over the water viewed from the 2026 North Shore Red Garden Party",
      caption: "The evening view"
    }
  ]
};

export const followAlong = {
  heading: "The latest from North Shore Red.",
  body:
    "North Shore Red's social channels publish conservative commentary on policies and public statements from Democratic officials, often using humor and a pointed tone, alongside occasional event and outreach updates.",
  links: [
    { title: "Facebook", body: "Longer commentary, policy comparisons and organization news.", href: "facebook" as const },
    { title: "Instagram", body: "Visual commentary on politics, policy and public statements.", href: "instagram" as const },
    { title: "X", body: "Short commentary and reactions — @northshore_red.", href: "x" as const }
  ]
};
