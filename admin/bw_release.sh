if [ -z $1 ]; then
	$1 = "bitweaver"
fi

git clone git@github.com:bitweaver/bitweaver.git -b RELEASE $1
cd $1
git submodule update --init --recursive
find . -name .git -exec rm -Rf {} \;
mkdir config/kernel;
