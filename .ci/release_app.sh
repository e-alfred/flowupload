#! /bin/bash

set -u
set -e

if [ -z ${1} ]; then
	echo "Release version (arg1) not set !"
	exit 1;
fi

SRC_DIR=`dirname $0`"/.."
RELEASE_VERSION=${1}
echo "Release version set to ${RELEASE_VERSION}"

sed -ri 's/(.*)<version>(.+)<\/version>/\1<version>'${RELEASE_VERSION}'<\/version>/g' ${SRC_DIR}/appinfo/info.xml
git commit -am "Release "${RELEASE_VERSION}
git tag ${RELEASE_VERSION}
git push
git push --tags
# Wait a second for Github to ingest our data
sleep 1
cd /tmp
rm -Rf flowupload-packaging && mkdir flowupload-packaging && cd flowupload-packaging

# Download the git file from github
wget https://github.com/e-alfred/flowupload/archive/${RELEASE_VERSION}.tar.gz
tar xzf ${RELEASE_VERSION}.tar.gz
mv flowupload-${RELEASE_VERSION} flowupload

cd /tmp/flowupload-packaging/flowupload
npm ci
npm run build

cd ..

# Drop unneeded files
rm -Rf \
	flowupload/gulpfile.js \
	flowupload/package.json \
	flowupload/node_modules \
	flowupload/package-lock.json \
	flowupload/.ci \
	flowupload/.tx \
	flowupload/doc

tar cfz flowupload-${RELEASE_VERSION}.tar.gz flowupload
echo "Release version "${RELEASE_VERSION}" is now ready."
