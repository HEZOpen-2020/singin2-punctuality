namespace F_stl {
    #ifndef _FSTL_LIB_MULTIPLIER
    #define _FSTL_LIB_MULTIPLIER

    //异或符号类 
    template<class T>
    struct Tp_xor {
        T operator() (const T &a,const T &b) const {
            return a^b;
        }
    };

    //加法符号类
    template<class T>
    struct Tp_sum {
        T operator() (const T &a,const T &b) const {
            return a+b;
        }
    };
    
    #endif
};
