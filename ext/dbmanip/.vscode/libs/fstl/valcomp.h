///// BEGIN Fake Sometimes TLE Library Valcomp /////
namespace F_stl {
    #ifndef _FSTL_LIB_VALCOMP
    #define _FSTL_LIB_VALCOMP
    //Comp类（小于号）
    template<class T>
    struct ValComp_less {
        inline bool operator() (const T &a,const T &b) {return a<b;}
    };

    //Comp类（大于号）
    template<class T>
    struct ValComp_greater {
        inline bool operator() (const T &a,const T &b) {return a>b;}
    };
    #endif
}
///// END Fake Sometimes TLE Library ValComp /////
