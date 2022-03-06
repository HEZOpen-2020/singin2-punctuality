#include "valcomp.h"
#include "rotatetreap.h"
///// BEGIN Fake Sometimes TLE Library Sbtree /////
namespace F_stl {
    #ifndef _FSTL_LIB_SBTREE
    #define _FSTL_LIB_SBTREE

    // 通用树套树类
    template<class T,T mi,T mx,class CT=ValComp_less<T> >
    struct Sbtree {
        // 线段树结点
        private: struct Node {
            RotateTreap<T,CT> v;
            int l,r;
            Node *L,*R;
        };

        // 线段树的根
        private: Node *rt;

        // 极值维护
        T Xmx;
        T Xmi;

        // 更新极值
        private: inline void update_range(T val) {
            Xmx=max(Xmx,val);
            Xmi=min(Xmi,val);
        }

        // 迭代器类型
        typedef typename RotateTreap<T,CT>::iterator TTp;

        // 销毁子树
        private: void __destroyTree(Node *t) {
            if(!t) return;
            __destroyTree(t->L);
            __destroyTree(t->R);
            delete t;
        }

        // 清空（擦除所有数据）
        public: inline void clear() {
            __destroyTree(rt);rt=NULL;
            Xmx=mi;Xmi=mx;
        }

        // 构造树
        private:template<class Iter>
        void __buildTree(int l,int r,Iter __init,Node *&t,bool flag) {
            if(!t) t=new Node();
            t->l = l;
            t->r = r;
            for(int i=l;i<=r;i++) {
                T &val=flag?(*(__init+i)):(*__init);
                t->v.insert(val);
                update_range(val);
            }
            if(l==r) return;
            int mid=(l+r)/2;
            __buildTree(l,mid,__init,t->L,flag);
            __buildTree(mid+1,r,__init,t->R,flag);
        }

        // 构造liter到riter（不包含riter）的树套树
        public:template<class Iter>
        inline void build(Iter liter,Iter riter) {
            clear();
            if(riter-liter == 0) return;
            __buildTree(0,riter-liter-1,liter,rt,1);
        }

        // 构造值为liter的，大小为sz的树套树
        public:template<class Iter>
        inline void build_init(Iter liter,int sz) {
            clear();
            if(sz == 0)return;
            __buildTree(0,sz-1,liter,rt,0);
        }

        // 获取大小
        public:inline int size() {
            if(!rt) return 0;
            return rt->r+1;
        }

        // 获取排名
        private: int __rk(int xl,int xr,T v,Node *t) {
            int l=t->l,r=t->r;
            if(xl==l && xr==r) {
                return t->v.rk(v)-1;
            }
            int mid=(l+r)/2;
            int ret=0;
            if(xl<=mid) ret+=__rk(xl,min(mid,xr),v,t->L);
            if(xr >mid) ret+=__rk(max(mid+1,xl),xr,v,t->R);
            return ret;
        }

        // 获取排名
        public: inline int rk(int xl,int xr,T v) {
            return __rk(xl,xr,v,rt)+1;
        }

        // 获取对应排名的值
        public: inline T atrk(int xl,int xr,T urk) {
            T l=Xmi-1,r=Xmx+1;
            while(l<r) {
                T mid=l+r-(l+r)/2;
                int xrk = __rk(xl,xr,mid,rt)+1;
                // cerr<<"[Debug] iakioi "<<l<<' '<<r<<' '<<mid<<" == "<<xrk<<endl;
                if(xrk>urk) r=mid-1;
                else l=mid;
            }
            return l;
        }

        // 修改单点值（返回值：修改前）
        private: T __update(int xc,T val,Node *t) {
            int l=t->l,r=t->r;
            if(xc==l && xc==r) {
                T ret = *(t->v.atrk(1));
                t->v.erase(t->v.find(ret),false);
                t->v.insert(val);
                update_range(val);
                return ret;
            }
            T ret;
            int mid=(l+r)/2;
            if(xc<=mid) ret=__update(xc,val,t->L);
            else        ret=__update(xc,val,t->R);
            t->v.erase(t->v.find(ret),false);
            t->v.insert(val);
            return ret;
        }

        // 修改单点值
        public: inline void update(int xc,T val) {
            __update(xc,val,rt);
        }

        // 获取前驱
        private: T __prev(int xl,int xr,T v,Node *t) {
            int l=t->l,r=t->r;
            if(xl==l && xr==r) {
                TTp it = (t->v.prev_value(v));
                if(!it) return mi;
                else return (*it);
            }
            int mid=(l+r)/2;
            T ret=mi;
            if(xl<=mid) ret=max(ret,__prev(xl,min(mid,xr),v,t->L));
            if(xr >mid) ret=max(ret,__prev(max(mid+1,xl),xr,v,t->R));
            return ret;
        }
        // 获取前驱
        public: inline T prev(int xl,int xr,T v) {
            return __prev(xl,xr,v,rt);
        }

        // 获取后继
        private: T __next(int xl,int xr,T v,Node *t) {
            int l=t->l,r=t->r;
            if(xl==l && xr==r) {
                TTp it = (t->v.next_value(v));
                if(!it) return mx;
                else return (*it);
            }
            int mid=(l+r)/2;
            T ret=mx;
            if(xl<=mid) ret=min(ret,__next(xl,min(mid,xr),v,t->L));
            if(xr >mid) ret=min(ret,__next(max(mid+1,xl),xr,v,t->R));
            return ret;
        }
        // 获取后继
        public: inline T next(int xl,int xr,T v) {
            return __next(xl,xr,v,rt);
        }
    };

    #endif
};
///// END Fake Sometimes TLE Library Sbtree /////
