if [ -z $1 ]; then
	echo usage $0 dirname
	exit
fi

git clone git@github.com:bitweaver/bitweaver.git -b RELEASE $1
cd $1
git submodule update --init --recursive
find . -name .git -exec rm -Rf {} \;
