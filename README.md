<!--
  - SPDX-FileCopyrightText: 2016 Nextcloud GmbH and Nextcloud contributors
  - SPDX-License-Identifier: AGPL-3.0-or-later
-->
# Usage survey client

[![REUSE status](https://api.reuse.software/badge/github.com/nextcloud/survey_client)](https://api.reuse.software/info/github.com/nextcloud/survey_client)

Sends anonymized data to Nextcloud to help us to improve Nextcloud. You
always have full control over the content sent to Nextcloud and can disable
it again at any time.

### Content being sent:
- App list (for each app: name, version, enabled status)
- Database environment (type, version, database size)
- Encryption information (is it enabled?, what is the default module)
- Number of shares (per type and permission setting)
- PHP environment (version, memory limit, max. execution time, max. file size)
- Server instance details (version, memcache used, status of locking/previews/avatars)
- Statistic (number of files, users, storages per type, comments and tags)


## QA metrics on master branch:

[![Build Status](https://travis-ci.org/nextcloud/survey_client.svg?branch=master)](https://travis-ci.org/nextcloud/survey_client)
