# 1 "<stdin>"
# 1 "<built-in>"
# 1 "<command-line>"
# 1 "<stdin>"

namespace F_stl {



    template<class T>
    struct ValComp_less {
        inline bool operator() (const T &a,const T &b) {return a<b;}
    };


    template<class T>
    struct ValComp_greater {
        inline bool operator() (const T &a,const T &b) {return a>b;}
    };

}
