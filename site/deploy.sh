#!/usr/bin/env bash
#
# Deploy the One More Tree site content into the WordPress container.
#
#   ./site/deploy.sh
#
# The container only mounts ./wp-content, so content sources in ./site are
# copied in first and then applied. Safe to re-run: everything is idempotent.

set -euo pipefail

CONTAINER="${WP_CONTAINER:-one-more-tree-wp}"
HERE="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
WP_ROOT="/var/www/html"
STAGE="/tmp/omt-content"

echo "==> Checking container ${CONTAINER}"
if ! docker inspect -f '{{.State.Running}}' "${CONTAINER}" 2>/dev/null | grep -q true; then
  echo "ERROR: container ${CONTAINER} is not running." >&2
  exit 1
fi

echo "==> Syncing form plugin into wp-content/mu-plugins"
# mu-plugins live inside the mounted wp-content, so they are already visible
# in the container; this is only a sanity check.
if [ ! -f "${HERE}/../wp-content/mu-plugins/omt-forms.php" ]; then
  echo "ERROR: wp-content/mu-plugins/omt-forms.php is missing." >&2
  exit 1
fi

echo "==> Staging content at ${STAGE}"
docker exec "${CONTAINER}" sh -c "rm -rf ${STAGE} && mkdir -p ${STAGE}"
docker cp "${HERE}/content" "${CONTAINER}:${STAGE}/content"
docker cp "${HERE}/apply.php" "${CONTAINER}:${STAGE}/apply.php"
if [ -f "${HERE}/validate.php" ]; then
  docker cp "${HERE}/validate.php" "${CONTAINER}:${STAGE}/validate.php"
fi

echo "==> Linting PHP"
docker exec "${CONTAINER}" sh -c "php -l ${WP_ROOT}/wp-content/mu-plugins/omt-forms.php && php -l ${STAGE}/apply.php"

echo "==> Applying content"
docker exec "${CONTAINER}" php "${STAGE}/apply.php" "${STAGE}/content"

echo "==> Verifying"
docker exec "${CONTAINER}" php "${STAGE}/validate.php"

echo "==> Done"
