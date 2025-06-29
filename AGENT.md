# Agent Configuration

## Commands
- Build: `npm run build` / `yarn build` / `pnpm build`
- Test: `npm test` / `yarn test` / `pnpm test`
- Test single file: `npm test -- <filename>` / `jest <filename>`
- Lint: `npm run lint` / `yarn lint` / `pnpm lint`
- Format: `npm run format` / `prettier --write .`

## Architecture
- Frontend: [framework/location]
- Backend: [framework/location]
- Database: [type/connection]
- API: [REST/GraphQL endpoints]

## Code Style
- Use TypeScript/JavaScript strict mode
- Prefer const over let, avoid var
- Use async/await over Promises.then()
- Follow existing naming conventions (camelCase/PascalCase)
- Import order: external libraries, then internal modules
- Handle errors explicitly with try/catch or proper error boundaries
- Use consistent formatting (Prettier/ESLint)
