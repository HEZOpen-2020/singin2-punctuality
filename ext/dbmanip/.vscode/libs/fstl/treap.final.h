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

namespace F_stl {



    template<class T,class ET>
    struct TreapNode {

        ET tag;


        TreapNode *fa,*ch[2];


        T val;

        int rank;

        int sz;


        int tmp;


        TreapNode() {
            sz=0;
            ch[0]=ch[1]=fa=NULL;
            rank=rand();
        }

        TreapNode(T _val) {
            sz=1;
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


    struct TreapRange_void {
        inline static bool enable() {return 0;}
        inline bool notNull() {return 0;}
        inline void add(TreapRange_void t) {}
        template<class Tp>
        inline void apply(Tp *ptr) {}
        inline void clear() {}
    };






    template<class T,class ET=TreapRange_void,class CT=ValComp_less<T> >
    struct Treap {
    private:

        typedef TreapNode<T,ET> Node;


        CT comp;


        static const int INF=0x7fffffff;


        bool enable_et;


        Node *rt;

    public:

        static const bool ERASE_ALL=true;


        static const bool ERASE_ONE=false;

    private:

        inline void push_down(Node *p) {
            if(!enable_et) return;
            if(!p) return;
            if(!(p->tag.notNull())) return;
            if(p->ch[0]) p->ch[0]->tag.add(p->tag);
            if(p->ch[1]) p->ch[1]->tag.add(p->tag);
            p->tag.apply(p);
            p->tag.clear();
        }


        inline void push_up(Node *p) {
            push_down(p->ch[0]);push_down(p->ch[1]);
            p->sz = (p->ch[0]?p->ch[0]->sz:0)+(p->ch[1]?p->ch[1]->sz:0)+1;
            if(p->ch[0]) p->ch[0]->fa=p;
            if(p->ch[1]) p->ch[1]->fa=p;
        }



        void split(int val,Node *curr,Node *&L,Node *&R,bool flag=0) {
            if(!curr) {L=R=NULL;return;}
            push_down(curr);
            if(flag?comp(curr->val,val):(!comp(val,curr->val))) {
                L=curr;
                split(val,curr->ch[1],curr->ch[1],R,flag);
            }
            else {
                R=curr;
                split(val,curr->ch[0],L,curr->ch[0],flag);
            }
            push_up(curr);
        }



        void rk_split(int rk,Node *curr,Node *&L,Node *&R) {
            if(!curr) {L=R=NULL;return;}
            push_down(curr);
            int lsz=(curr->ch[0]?(curr->ch[0]->sz):(0));
            if(rk<=lsz) {
                R=curr;
                rk_split(rk,curr->ch[0],L,curr->ch[0]);
            }
            else {
                L=curr;
                rk_split(rk-lsz-1,curr->ch[1],curr->ch[1],R);
            }
            push_up(curr);
        }



        void tmp_split(Node *curr,Node *&L,Node *&R,bool flag=0,int tt=-1) {
            if(!curr) {L=R=NULL;return;}
            push_down(curr);
            if((curr->tmp==0 || (flag && curr->tmp==-1) || tt==0) && tt!=1) {
                R=curr;
                if(curr->tmp==-1 && tt==-1) tt=1;
                tmp_split(curr->ch[0],L,curr->ch[0],flag,tt);
            }
            else {
                L=curr;
                if(curr->tmp==-1 && tt==-1) tt=0;
                tmp_split(curr->ch[1],curr->ch[1],R,flag,tt);
            }
            push_up(curr);
        }



        inline void iter_split(Node *t,Node *h,Node *&L,Node *&R,bool flag=0) {
            t->tmp = -1;
            while(t->fa) {
                t->fa->tmp = t->identify();
                t = t->fa;
            }
            tmp_split(h,L,R,flag);
        }


        Node *merge(Node *a,Node *b) {
            if(a) push_down(a);
            if(b) push_down(b);
            if(!b) return a;
            if(!a) return b;
            if((a->rank) < (b->rank)) {
                a->ch[1] = merge(a->ch[1],b);
                push_up(a);
                return a;
            }
            else {
                b->ch[0] = merge(a,b->ch[0]);
                push_up(b);
                return b;
            }
        }


        void __destroyTree(Node *t) {
            if(!t) return;
            push_down(t);
            if(t->ch[0]) __destroyTree(t->ch[0]);
            if(t->ch[1]) __destroyTree(t->ch[1]);
            delete t;
        }

    public:





        struct iterator {
        public:
            Node *it;
            Treap<T,ET,CT> *tr;
            iterator() {it=NULL;}
            iterator(Node *_it,Treap<T,ET,CT> *_tr) {it=_it;tr=_tr;}


            inline bool isNull() const {return !(it);}


            inline const T &operator*() const {
                return it->val;
            }


            inline iterator &operator++() {
                (*this) = (*this)+1;
                return *this;
            }


            inline iterator operator++(signed __suf) {
                iterator ret=(*this);
                ++(*this);
                return ret;
            }


            inline iterator &operator--() {
                (*this) = (*this)-1;
                return *this;
            }


            inline iterator operator--(signed __suf) {
                iterator ret=(*this);
                --(*this);
                return ret;
            }


            inline bool operator==(const iterator &other) const {
                return it==other.it && tr==other.tr;
            }


            inline bool operator!=(const iterator &other) const {
                return !operator==(other);
            }


            inline bool operator!() const {
                return isNull();
            }


            inline iterator operator+(const int &other) const {
                if(isNull()) return iterator(NULL,tr);
                return tr->atrk(tr->idx(*this) + 1 + other);
            }
            inline iterator operator+=(const int &other) {
                (*this) = (*this)+other;
                return (*this);
            }


            inline iterator operator-(const int &other) const {
                if(isNull()) return iterator(NULL,tr);
                return tr->atrk(tr->idx(*this) + 1 - other);
            }
            inline iterator operator-=(const int &other) {
                (*this) = (*this)-other;
                return (*this);
            }


            inline int operator-(const iterator &other) const {
                if(isNull() || other.isNull()) return -1;
                if(tr != other.tr) return -1;
                return tr->idx(*this) - other.tr->idx(other);
            }


            inline const Node *operator->() const {
                return &(it->val);
            }
        };


        inline iterator begin() {
            return atrk(1);
        }


        inline iterator end() {
            return iterator(NULL,this);
        }



        inline iterator rbegin() {
            return atrk(size());
        }


        inline iterator rend() {
            return iterator(NULL,this);
        }


        inline void clear() {
            __destroyTree(rt->ch[1]);
            rt->ch[1] = NULL;
        }


        inline int size() {
            if(!rt->ch[1]) return 0;
            push_down(rt->ch[1]);
            return rt->ch[1]->sz;
        }


        Treap() {
            rt=new Node();
            rt->rank=-INF;
            enable_et = ET::enable();
        }



        inline iterator find(T val,bool flag=0) {
            Node *x,*y,*ret;
            if(!flag) {
                split(val,rt->ch[1],x,y,true);
                ret = y;
                while(ret && ret->ch[0]) ret = ret->ch[0];
            }
            else {
                split(val,rt->ch[1],x,y);
                ret = x;
                while(ret && ret->ch[1]) ret = ret->ch[1];
            }
            rt->ch[1] = merge(x,y);
            if(ret && !comp(ret->val,val) && !comp(val,ret->val)) return iterator(ret,this);
            else return iterator(NULL,this);
        }





        inline iterator insert(T val) {
            Node *L,*R;
            split(val,rt->ch[1],L,R);
            Node *ret = new Node(val);
            rt->ch[1] = merge(merge(L,ret),R);
            return iterator(ret,this);
        }



        inline iterator insert(iterator pos,T val,bool flag=0) {
            Node *L,*R;
            iter_split(pos.it,rt->ch[1],L,R,!flag);
            Node *ret = new Node(val);
            rt->ch[1] = merge(merge(L,ret),R);
            return iterator(ret,this);
        }



        inline iterator rk_insert(int pos,T val,bool flag=0) {
            Node *L,*R;
            rk_split(flag?(pos):(pos-1),rt->ch[1],L,R);
            Node *ret = new Node(val);
            rt->ch[1] = merge(merge(L,ret),R);
            return iterator(ret,this);
        }


        inline int count(T val) {
            Node *x,*y,*z;
            split((val),rt->ch[1],x,z);
            split((val),x,x,y,true);
            int ret = y?(y->sz):(0);
            rt->ch[1] = merge(merge(x,y),z);
            return ret;
        }






        inline void erase(iterator it,bool tp=false) {
            Node *x,*y,*z;
            if(tp) {
                split((*it),rt->ch[1],x,z);
                split((*it),x,x,y,true);
                __destroyTree(y);
                rt->ch[1] = merge(x,z);
            }
            else {
                iter_split(it.it,rt->ch[1],x,z);
                iter_split(it.it,x,x,y,true);

                assert(y);
                Node *y_ = y;
                y = merge(y->ch[0],y->ch[1]);
                delete y_;
                rt->ch[1] = merge(merge(x,y),z);
            }
        }






        inline void erase_value(T val,bool tp=false) {
            Node *x,*y,*z;
            split(val,rt->ch[1],x,z);
            split(val,x,x,y,true);
            if(tp) {__destroyTree(y);y=NULL;}
            else {Node *y_=y;y=merge(y->ch[0],y->ch[1]);delete y_;}
            rt->ch[1] = merge(merge(x,y),z);
        }


        inline void erase_rk(int lrk,int rrk) {
            Node *x,*y,*z;
            rk_split(rrk,rt->ch[1],x,z);
            rk_split(lrk-1,x,x,y);
            __destroyTree(y);
            rt->ch[1] = merge(x,z);
        }


        inline void erase(iterator liter,iterator riter) {
            Node *x,*y,*z;
            iter_split(riter.it,rt->ch[1],x,z);
            iter_split(liter.it,x,x,y,true);
            __destroyTree(y);
            rt->ch[1] = merge(x,z);
        }


        inline int rk(T val) {
            Node *x,*y;
            split(val,rt->ch[1],x,y,true);
            int ret = (x?(x->sz):(0)) + 1;
            rt->ch[1] = merge(x,y);
            return ret;
        }


        inline iterator atrk(int urk) {
            Node *curr=rt->ch[1];
            if(!curr) return iterator(NULL,this);
            while(1) {
                push_down(curr);
                int lsz=(curr->ch[0]?curr->ch[0]->sz:0);
                int l= lsz+1;
                if((urk>lsz) && urk<=l) break;
                if(urk<l) curr=curr->ch[0];
                else {
                    urk-=l;
                    curr=curr->ch[1];
                }
                if(!curr) return iterator(NULL,this);
            }
            return iterator(curr,this);
        }


        inline iterator prev_value(T val) {
            Node *x,*y;
            split(val,rt->ch[1],x,y,true);
            Node *ret=x;
            while(ret && ret->ch[1]) ret=ret->ch[1];
            rt->ch[1]=merge(x,y);
            return iterator(ret,this);
        }


        inline iterator next_value(T val) {
            Node *x,*y;
            split(val,rt->ch[1],x,y);
            Node *ret=y;
            while(ret && ret->ch[0]) ret=ret->ch[0];
            rt->ch[1]=merge(x,y);
            return iterator(ret,this);
        }


        inline iterator upper_bound(T val) {
            return next_value(val);
        }


        inline iterator lower_bound(T val) {
            Node *x,*y;
            split(val,rt->ch[1],x,y,true);
            Node *ret=y;
            while(ret && ret->ch[0]) ret=ret->ch[0];
            rt->ch[1]=merge(x,y);
            return iterator(ret,this);
        }



        inline void operation(iterator liter,iterator riter,ET xxx) {
            Node *x,*y,*z;
            iter_split(riter.it,rt->ch[1],x,z);
            iter_split(liter.it,x,x,y,true);

            if(y) y->tag.add(xxx);
            rt->ch[1] = merge(merge(x,y),z);
        }



        inline void operation(int lrk,int rrk,ET xxx) {
            Node *x,*y,*z;
            rk_split(rrk,rt->ch[1],x,z);
            rk_split(lrk-1,x,x,y);

            if(y) y->tag.add(xxx);
            rt->ch[1] = merge(merge(x,y),z);
        }


        inline const T &operator[] (int idx) {
            return (*atrk(idx+1));
        }


        inline int idx(iterator it) {
            if(it.isNull()) return -1;
            Node *x,*y;
            iter_split(it.it,rt->ch[1],x,y,true);

            int ret=x?(x->sz):0;
            rt->ch[1]=merge(x,y);
            return ret;
        }


        inline pair<iterator,iterator> equal_range(T val) {
            return make_pair(lower_bound(val),upper_bound(val));
        }

    private:

        int dump_idx;
        void __print(Node *n) {
            push_down(n);
            if(n->ch[0]) __print(n->ch[0]);
            dump_idx++;
            cout<<dump_idx<<"\t"<<(n->val)<<"\t"<<n<<endl;
            if(n->ch[1]) __print(n->ch[1]);
        }

    public:

        void print_r(Node *t=NULL) {
            if(!t) {
                cout<<"Tree is empty."<<endl;
                return;
            }
            cout<<"Index\tValue\tPointer"<<endl;
            dump_idx=0;
            __print(t);
        }


        void print() {print_r(rt->ch[1]);}
    };

};
