# 1 "<stdin>"
# 1 "<built-in>"
# 1 "<command-line>"
# 1 "<stdin>"
# 1 "valcomp.h" 1

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
# 2 "<stdin>" 2
# 1 "rotatetreap.h" 1
# 1 "valcomp.h" 1

namespace F_stl {
# 17 "valcomp.h"
}
# 2 "rotatetreap.h" 2

namespace F_stl {




    template<class T>
    struct RotateTreapNode {

        T __init;


        RotateTreapNode *fa,*ch[2];


        T val;

        int rank;

        int freq,sz;

        RotateTreapNode() {
            freq=sz=0;
            val=__init;
            ch[0]=ch[1]=fa=NULL;
            rank=rand();
        }

        RotateTreapNode(T _val) {
            freq=sz=1;
            val=_val;
            ch[0]=ch[1]=fa=NULL;
            rank=rand();
        }

        int identify() {
            if(!fa) return -1;
            if(fa->ch[0]==this) return 0;
            return 1;
        }
    };




    template<class T,class CT=ValComp_less<T> >
    struct RotateTreap {
    private:

        typedef RotateTreapNode<T> Node;


        static const int INF=0x7fffffff;


        CT comp;


        T __init;


        Node *rt;


        stack<Node*> clear_s;

    public:

        static const bool ERASE_ALL=true;


        static const bool ERASE_ONE=false;

    private:

        inline void push_up(Node *p) {
            p->sz = (p->ch[0]?p->ch[0]->sz:0)+(p->ch[1]?p->ch[1]->sz:0)+p->freq;
        }


        inline int identify(Node *p) {
            if(p->fa==NULL) return -1;
            if(p->fa->ch[0]==p) return 0;
            return 1;
        }


        inline void connect(Node *u,Node *f,int id) {
            f->ch[id]=u;
            u->fa=f;
        }


        inline void rotate(Node *x) {
            Node *y=x->fa;
            Node *R=y->fa;
            int Rs=identify(y);
            int ys=identify(x);
            Node *B=x->ch[!ys];
            if(B) connect(B,y,ys);
            else y->ch[ys]=NULL;
            connect(y,x,!ys);
            connect(x,R,Rs);
            push_up(y);
            push_up(x);
        }


        inline Node *__insert(T val) {
            Node *curr=rt->ch[1];
            if(!curr) {
                Node *ret=new Node(val);
                ret->fa=rt;rt->ch[1]=ret;
                return (ret);
            }
            while(1) {
                curr->sz++;
                if(!comp(curr->val,val) && !comp(val,curr->val)) {
                    curr->freq++;
                    return curr;
                }
                Node *nxt=curr;
                int nxtid=0;
                if(comp(val,curr->val)) nxtid=0;
                else nxtid=1;
                nxt=curr->ch[nxtid];
                if(!nxt) {
                    nxt=new Node(val);
                    curr->ch[nxtid]=nxt;nxt->fa=curr;
                    return (nxt);
                }
                curr=nxt;
            }
            return (NULL);
        }

    public:


        struct iterator {
        public:
            Node *it;
            int idx;
            iterator() {it=NULL;}
            iterator(Node *_it) {it=_it;idx=1;}
            iterator(Node *_it,int _idx) {it=_it;idx=_idx;}


            bool isNull() const {return !(it);}


            int freq() {
                if(isNull()) return 0;
                return it->freq;
            }


            iterator iter_next() {
                Node *it2=it;
                if(it2->ch[1]) {
                    Node *tmp=it2->ch[1];
                    while(tmp->ch[0]) {
                        tmp=tmp->ch[0];
                    }
                    return tmp;
                }
                while(it2->fa && it2->identify()==1) {it2=it2->fa;}
                if(!it2->fa) return iterator(NULL);
                return iterator(it2->fa);
            }


            iterator iter_prev() {
                Node *it2=it;
                if(it2->ch[0]) {
                    Node *tmp=it2->ch[0];
                    while(tmp->ch[1]) {
                        tmp=tmp->ch[1];
                    }
                    return tmp;
                }
                while(it2->fa && it2->identify()==0) {it2=it2->fa;}
                if(!it2->fa) return iterator(NULL);
                if(it2->fa->fa) return iterator(it2->fa);
                return iterator(NULL);
            }


            inline const T &operator*() const {
                return it->val;
            }


            inline iterator &operator++() {
                if(idx < (it->freq)) {idx++;return *this;}
                (*this) = (this->iter_next());
                idx=1;
                return *this;
            }


            inline iterator operator++(signed __suf) {
                iterator ret=(*this);
                ++(*this);
                return ret;
            }


            inline iterator &operator--() {
                if(idx > 1) {idx--;return *this;}
                (*this) = (this->iter_prev());
                if(it) idx=it->freq;
                return *this;
            }


            inline iterator operator--(signed __suf) {
                iterator ret=(*this);
                --(*this);
                return ret;
            }


            bool operator==(const iterator &other) const {
                if(it==NULL && it==other.it) return true;
                return (it==other.it) && (idx==other.idx);
            }


            bool operator!=(const iterator &other) const {
                return !operator==(other);
            }


            bool operator!() const {
                return isNull();
            }


            const T *operator->() const {
                return &(it->val);
            }
        };


        inline iterator begin() {
            Node *curr=rt->ch[1];
            if(!curr) return iterator(NULL);
            while(curr->ch[0]) curr=curr->ch[0];
            return iterator(curr,1);
        }


        inline iterator end() {
            return iterator(NULL,0);
        }



        inline iterator rbegin() {
            Node *curr=rt->ch[1];
            if(!curr) return iterator(NULL);
            while(curr->ch[1]) curr=curr->ch[1];
            return iterator(curr,curr->freq);
        }


        inline iterator rend() {
            return iterator(NULL,0);
        }


        inline void clear() {
            if(!rt->ch[1]) return;
            clear_s.push(rt->ch[1]);
            while(!clear_s.empty()) {
                Node *curr=clear_s.top();
                if(curr->ch[0]) {
                    clear_s.push(curr->ch[0]);
                    curr->ch[0]=NULL;
                }
                else if(curr->ch[1]) {
                    clear_s.push(curr->ch[1]);
                    curr->ch[1]=NULL;
                }
                else {
                    delete curr;
                    clear_s.pop();
                }
            }
            rt->ch[1]=NULL;
        }


        inline int size() {
            if(!rt->ch[1]) return 0;
            return rt->ch[1]->sz;
        }


        RotateTreap() {
            rt=new Node();
            rt->rank=-INF;
        }


        inline iterator find(T val) {
            Node *curr=rt->ch[1];
            if(!curr) return iterator(NULL);
            while(1) {
                if(!comp(curr->val,val) && !comp(val,curr->val)) {
                    return iterator(curr);
                }
                if(val < curr->val) curr=curr->ch[0];
                else curr=curr->ch[1];
                if(!curr) return iterator(NULL);
            }
        }






        inline iterator insert_t(T val) {
            return iterator(__insert(val));
        }





        inline iterator insert(T val) {
            Node *ret = __insert(val);
            while((ret->rank) < (ret->fa->rank)) {
                rotate(ret);
            }
            return iterator(ret);
        }






        inline void erase(iterator it,bool tp=ERASE_ONE) {
            Node *s = it.it;
            s->sz--;
            s->freq--;
            if(tp) s->freq=0;
            Node *dd=s->fa;
            if(s->freq==0) {
                while(1) {
                    int nxtid=0;
                    if(!s->ch[0]) nxtid=1;
                    else if(s->ch[1] && (s->ch[1]->rank) < (s->ch[0]->rank)) {
                        nxtid=1;
                    }
                    if(!s->ch[nxtid]) {
                        dd=s->fa;
                        dd->ch[identify(s)]=NULL;
                        delete s;
                        break;
                    }
                    else rotate(s->ch[nxtid]);
                }
            }
            while(dd) {
                push_up(dd);
                dd=dd->fa;
            }
        }


        inline int rk(T val) {
            int ret=0;
            Node *curr=rt->ch[1];
            if(!curr) return 1;
            while(1) {
                if(comp(curr->val,val)) {
                    ret+=curr->freq;
                    if(curr->ch[0]) ret+=curr->ch[0]->sz;
                    curr=curr->ch[1];
                }
                else {
                    curr=curr->ch[0];
                }
                if(!curr) return ret+1;
            }
        }


        inline iterator atrk(int urk) {
            Node *curr=rt->ch[1];
            if(!curr) return iterator(NULL);
            while(1) {
                int l= (curr->sz) - (curr->ch[1]?(curr->ch[1]->sz):0);
                if((urk>(curr->ch[0]?curr->ch[0]->sz:0)) && urk<=l) break;
                if(urk<l) curr=curr->ch[0];
                else {
                    urk-=l;
                    curr=curr->ch[1];
                }
                if(!curr) return iterator(NULL);
            }
            return iterator(curr);
        }


        inline iterator prev_value(T val) {
            iterator tmp=insert_t(val);
            iterator ret=tmp.iter_prev();
            erase(tmp,ERASE_ONE);
            return ret;
        }


        inline iterator next_value(T val) {
            iterator tmp=insert_t(val);
            iterator ret=tmp.iter_next();
            erase(tmp,ERASE_ONE);
            return ret;
        }


        inline iterator upper_bound(T val) {
            return next_value(val);
        }


        inline iterator lower_bound(T val) {
            iterator ret=find(val);
            if(!ret.isNull()) return ret;
            return next_value(val);
        }

    private:

        int dump_idx;
        void __print(Node *n) {
            if(n->ch[0]) __print(n->ch[0]);
            dump_idx++;
            cout<<dump_idx<<"\t"<<(n->val)<<endl;
            if(n->ch[1]) __print(n->ch[1]);
        }

    public:

        inline void print() {
            if(!rt->ch[1]) {
                cout<<"Tree is empty."<<endl;
                return;
            }
            cout<<"Index\tValue"<<endl;
            dump_idx=0;
            __print(rt->ch[1]);
        }
    };


};
# 3 "<stdin>" 2

namespace F_stl {




    template<class T,T mi,T mx,class CT=ValComp_less<T> >
    struct Sbtree {

        private: struct Node {
            RotateTreap<T,CT> v;
            int l,r;
            Node *L,*R;
        };


        private: Node *rt;


        T Xmx;
        T Xmi;


        private: inline void update_range(T val) {
            Xmx=max(Xmx,val);
            Xmi=min(Xmi,val);
        }


        typedef typename RotateTreap<T,CT>::iterator TTp;


        private: void __destroyTree(Node *t) {
            if(!t) return;
            __destroyTree(t->L);
            __destroyTree(t->R);
            delete t;
        }


        public: inline void clear() {
            __destroyTree(rt);rt=NULL;
            Xmx=mi;Xmi=mx;
        }


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


        public:template<class Iter>
        inline void build(Iter liter,Iter riter) {
            clear();
            if(riter-liter == 0) return;
            __buildTree(0,riter-liter-1,liter,rt,1);
        }


        public:template<class Iter>
        inline void build_init(Iter liter,int sz) {
            clear();
            if(sz == 0)return;
            __buildTree(0,sz-1,liter,rt,0);
        }


        public:inline int size() {
            if(!rt) return 0;
            return rt->r+1;
        }


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


        public: inline int rk(int xl,int xr,T v) {
            return __rk(xl,xr,v,rt)+1;
        }


        public: inline T atrk(int xl,int xr,T urk) {
            T l=Xmi-1,r=Xmx+1;
            while(l<r) {
                T mid=l+r-(l+r)/2;
                int xrk = __rk(xl,xr,mid,rt)+1;

                if(xrk>urk) r=mid-1;
                else l=mid;
            }
            return l;
        }


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
            else ret=__update(xc,val,t->R);
            t->v.erase(t->v.find(ret),false);
            t->v.insert(val);
            return ret;
        }


        public: inline void update(int xc,T val) {
            __update(xc,val,rt);
        }


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

        public: inline T prev(int xl,int xr,T v) {
            return __prev(xl,xr,v,rt);
        }


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

        public: inline T next(int xl,int xr,T v) {
            return __next(xl,xr,v,rt);
        }
    };


};
