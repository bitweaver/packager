git clone git@github.com:bitweaver/bitweaver.git -b RELEASE bwrelease
cd bwrelease
git submodule update --init --recursive
find . -name .git -exec rm -Rf {} \;
