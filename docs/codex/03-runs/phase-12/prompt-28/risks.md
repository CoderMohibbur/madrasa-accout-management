# Risks

- Shared primitives now exist, but several management pages still define page-local visual tokens and will need later adoption to complete consistency.
- Donor and guardian dashboards/lists/details still use legacy dark content panels inside the new shared shell until their dedicated normalization prompts land.
- Auth views still have some page-local checkbox and link styling that the later auth-adoption prompt should fold into the shared primitives.
- Local validation depends on the Laragon PHP 8.2 runtime because the local 8.4 runtime in this environment does not currently load `mbstring`.
