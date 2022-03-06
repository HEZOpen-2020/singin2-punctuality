#include "valcomp.h"

namespace F_stl {
    #ifndef _FSTL_LIB_LEFTISTTREE
    #define _FSTL_LIB_LEFTISTTREE

    // 通用左偏树类
    template<class T,class CT = ValComp_less<T> >
    struct LeftistTree {
    private:
        // 结点
        struct Node {
            int dist,idx;
            T val;
            Node *L,*R;
            Node *rtp;
            Node() {L=R=NULL;dist=1;idx=0;rtp=NULL;}
            Node(T _val,int _idx) {L=R=NULL;dist=1;val=_val;idx=_idx;rtp=NULL;}
        };

        // 比较器
        CT comp;

        // 指针数据存储
        vector<Node*> data;

        // 销毁结点
        inline void __clear(Node *t) {
            if(!t) return;
            try{
                __clear(t->L);__clear(t->R);
                delete t;
            }catch(exception e) {}
        }

        // 递归并堆
        Node *__merge(Node *x,Node *y) {
            if(x==NULL || y==NULL) {
                return x?x:y;
            }
            if(comp(x->val,y->val)) swap(x,y);
            x->R = __merge(x->R,y);
            if((x->L?(x->L->dist):0) < (x->R?(x->R->dist):0)) {
                swap(x->L,x->R);
            }
            x->dist = (x->R?(x->R->dist):0)+1;
            return x;
        }

        Node *__rt(Node *t) {
            if(t->rtp == t) return t;
            return t->rtp=__rt(t->rtp);
        }

    public:
        // 快速模式指定量（不会再合并孤立后的点）
        bool fastmode;

        // 构造
        LeftistTree(){}
        
        // 清空，擦除数据
        inline void clear() {
            for(unsigned i=0;i<data.size();i++) __clear(data[i]);
            data.resize(0);
        }

        // 建立新的堆，包含元素val，返回下标。下标从0开始，向后连续分配
        inline int create(T val) {
            data.push_back(new Node(val,data.size()));
            int ret = data.size()-1;
            data[ret]->rtp = data[ret];
            return ret;
        }

        // 查找堆的根结点，返回下标
        inline int rt(unsigned idx) {
            return __rt(data[idx])->idx;
        }

        // 合并堆
        inline bool merge(unsigned x,unsigned y) {
            Node *a = __rt(data[x]),*b=__rt(data[y]);
            if(a==b) return 0;
            a->rtp = b->rtp = __merge(a,b);return 1;
        }

        // 取出最大值
        inline T top(unsigned idx) {
            return __rt(data[idx])->val;
        }

        // 孤立并返回最大值（从原有堆中去除）
        inline T pop(unsigned idx) {
            Node *curr = __rt(data[idx]);
            T ret = curr->val;
            Node *h = __merge(curr->L,curr->R);
            curr->rtp = h;
            if(curr->L) curr->L->rtp=h;
            if(curr->R) curr->R->rtp=h;
            curr->L=curr->R=NULL;
            curr->dist=0;
            if(!fastmode) data[curr->idx] = new Node(curr->val,curr->idx);
            return ret;
        }

        // 强制修改（注意，需要保证修改的值是孤立的）
        inline void update(unsigned idx,T val) {
            data[idx]->val = val;
        }

        // 询问单点值
        inline T query(unsigned idx) {
            return data[idx]->val;
        }

        // 询问深度
        inline int hh(unsigned idx) {
            return data[idx]->dist;
        }
    };

    #endif
};
