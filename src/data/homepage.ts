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
  dateLabel: "Save the date",
  month: "Sep",
  day: "28",
  weekday: "Sunday",
  time: "5:00 pm",
  name: "The North Shore Red Garden Party",
  description:
    "Our one large gathering of the year, keynoted by former Governor Tommy Thompson — the longest-serving governor in Wisconsin history. Tickets start at $100, and seats are limited.",
  details: [
    { label: "Tickets from", value: "$100" },
    { label: "Seating", value: "Limited" },
    { label: "Keynote", value: "Gov. Thompson" }
  ],
  hostNote: "Host location and program details go to invited guests.",
  footNote:
    "We hold one or two substantial events a year rather than a weekly calendar, and our gatherings are by invitation. Add your name to the invitation list and a board member will follow up — the day-to-day runs on our social accounts below."
};

export const followAlong = {
  heading: "Follow the work as it happens.",
  body:
    "North Shore Red's social channels carry event notices, photos from the field, candidate news, and short updates from the board. Choose a channel below to stay connected.",
  links: [
    { title: "Facebook", body: "Event notices and longer posts from the board.", href: "facebook" as const },
    { title: "Instagram", body: "Photos from canvasses, postcard nights and events.", href: "instagram" as const },
    { title: "X", body: "Short, fast updates and candidate news — @northshore_red.", href: "x" as const }
  ]
};
